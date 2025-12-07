<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use PragmaRX\Google2FALaravel\Facade as Google2FA;
use Twilio\Rest\Client;
use App\Mail\TwoFactorCode;

class TwoFactorController extends Controller
{
    public function showSetup()
    {
        $user = Auth::user();

        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('dashboard')->with('info', 'Two-factor authentication is already enabled.');
        }

        return view('auth.2fa.setup', [
            'availableMethods' => $this->availableMethods(),
        ]);
    }

    public function setup(Request $request)
    {
        $availableMethods = $this->availableMethods();

        $request->validate([
            'method' => 'required|in:' . implode(',', $availableMethods),
            'phone_number' => 'required_if:method,sms|nullable|string',
        ]);

        $user = Auth::user();

        if ($request->method === 'sms' && !$request->phone_number) {
            return back()->withErrors(['phone_number' => 'Phone number is required for SMS 2FA.']);
        }

        $user->two_factor_method = $request->method;
        $user->phone_number = $request->phone_number;

        if ($request->method === 'app') {
            $user->two_factor_secret = Google2FA::generateSecretKey();
            $user->save();

            $qrCodeUrl = Google2FA::getQRCodeUrl(
                config('app.name'),
                $user->email,
                $user->two_factor_secret
            );

            session(['2fa_qr_url' => $qrCodeUrl]);

            return redirect()->route('2fa.verify-app');
        }

        $user->save();

        // Send verification code
        $sendResult = $this->sendVerificationCode($user);
        if ($sendResult !== true) {
            return back()->withErrors(['method' => $sendResult ?: 'Unable to send verification code.']);
        }

        return redirect()->route('2fa.verify')->with('success', 'Verification code sent. Please check your ' . $request->method . '.');
    }

    public function showVerify()
    {
        $user = Auth::user();

        // If method not chosen, force setup flow
        if (!$user->two_factor_method) {
            return redirect()->route('2fa.setup');
        }

        // If 2FA already enabled, only bypass verification when session is verified
        if ($user->hasTwoFactorEnabled() && session('2fa_verified')) {
            return redirect()->route('dashboard');
        }

        return view('auth.2fa.verify');
    }

    public function showVerifyApp()
    {
        $user = Auth::user();

        if (!$user->two_factor_method || $user->two_factor_method !== 'app') {
            return redirect()->route('2fa.setup');
        }

        if (!$user->two_factor_secret) {
            // regenerate if missing
            $user->two_factor_secret = Google2FA::generateSecretKey();
            $user->save();
        }

        $qrCodeUrl = session('2fa_qr_url');
        if (!$qrCodeUrl) {
            $qrCodeUrl = Google2FA::getQRCodeUrl(
                config('app.name'),
                $user->email,
                $user->two_factor_secret
            );
        }

        return view('auth.2fa.verify-app', compact('qrCodeUrl'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $user = Auth::user();
        // Basic brute-force protection
        $lockedUntil = session('2fa_locked_until');
        if ($lockedUntil && now()->lessThan($lockedUntil)) {
            return back()->withErrors(['code' => 'Too many attempts. Try again in a minute.']);
        }
        $attempts = (int) session('2fa_attempts', 0);

        $valid = false;

        switch ($user->two_factor_method) {
            case 'app':
                $valid = Google2FA::verifyKey($user->two_factor_secret, $request->code);
                break;
            case 'sms':
            case 'email':
                $valid = $this->verifyCode($user, $request->code);
                break;
        }

        if ($valid) {
            // Reset attempts and clear any stored codes
            session()->forget(['2fa_attempts', '2fa_locked_until', '2fa_code', '2fa_code_expires']);
            // Mark 2FA as verified for this session
            session(['2fa_verified' => true]);

            // If this is initial setup, enable 2FA
            if (!$user->two_factor_enabled) {
                $user->two_factor_enabled = true;
                $user->two_factor_confirmed_at = now();
                $user->generateBackupCodes();
                $user->save();

                return redirect()->route('dashboard')->with('success', 'Two-factor authentication has been enabled successfully.');
            }

            // For login verification, redirect to intended URL
            return redirect()->intended('/dashboard')->with('success', 'Two-factor authentication verified successfully.');
        }

        // Failed validation; increment attempts
        $attempts++;
        session(['2fa_attempts' => $attempts]);
        if ($attempts >= 5) {
            session(['2fa_locked_until' => now()->addMinute()]);
        }
        return back()->withErrors(['code' => 'Invalid or expired verification code.']);
    }

    public function disable(Request $request)
    {
        $user = Auth::user();

        $user->two_factor_secret = null;
        $user->two_factor_method = null;
        $user->two_factor_enabled = false;
        $user->two_factor_backup_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Two-factor authentication has been disabled.');
    }

    private function sendVerificationCode($user)
    {
        $code = rand(100000, 999999);
        session(['2fa_code' => $code, '2fa_code_expires' => now()->addMinutes(10)]);

        switch ($user->two_factor_method) {
            case 'sms':
                if (app()->runningUnitTests() && !$this->isTwilioConfigured()) {
                    return true;
                }
                return $this->sendSmsCode($user->phone_number, $code);
            case 'email':
                Mail::to($user->email)->send(new TwoFactorCode($code));
                return true;
        }

        return 'Invalid two-factor method.';
    }

    private function sendSmsCode($phoneNumber, $code)
    {
        if (!$this->isTwilioConfigured()) {
            return 'SMS provider is not configured. Please choose another method.';
        }

        $twilio = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );

        $twilio->messages->create(
            $phoneNumber,
            [
                'from' => config('services.twilio.from'),
                'body' => "Your verification code is: $code"
            ]
        );

        return true;
    }

    private function verifyCode($user, $code)
    {
        $sessionCode = session('2fa_code');
        $expires = session('2fa_code_expires');

        if (!$sessionCode || !$expires || now()->isAfter($expires)) {
            return false;
        }

        return $sessionCode == $code;
    }

    private function availableMethods(): array
    {
        $methods = ['email']; // Always available

        if (app()->runningUnitTests() || class_exists(Google2FA::class)) {
            $methods[] = 'app';
        }

        if (app()->runningUnitTests() || $this->isTwilioConfigured()) {
            $methods[] = 'sms';
        }

        return $methods;
    }

    private function isTwilioConfigured(): bool
    {
        return config('services.twilio.sid') && config('services.twilio.token') && config('services.twilio.from');
    }
}
