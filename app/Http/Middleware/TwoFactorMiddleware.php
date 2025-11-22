<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Skip 2FA check for certain routes
        if ($this->shouldSkipTwoFactor($request)) {
            return $next($request);
        }

        // If user has 2FA enabled but hasn't completed verification
        if ($user && $user->hasTwoFactorEnabled() && !session('2fa_verified')) {
            // Store intended URL
            session(['url.intended' => $request->url()]);

            // Redirect to 2FA verification (single entry point)
            return redirect()->route('2fa.verify');
        }

        return $next($request);
    }

    /**
     * Check if the request should skip 2FA verification
     */
    private function shouldSkipTwoFactor(Request $request): bool
    {
        $skipRoutes = [
            'logout',
            '2fa.setup',
            '2fa.setup.post',
            '2fa.verify',
            '2fa.verify.post',
            // Do NOT skip '2fa.disable' to avoid bypassing verification
        ];

        return in_array($request->route()?->getName(), $skipRoutes);
    }
}
