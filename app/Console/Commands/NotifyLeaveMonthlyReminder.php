<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyLeaveMonthlyReminder extends Command
{
    protected $signature = 'notify:leave-monthly-reminder';
    protected $description = 'Kirim pengingat awal bulan untuk pengajuan cuti.';

    public function handle(): int
    {
        $senderId = $this->resolveSenderId();
        if (!$senderId) {
            $this->error('Tidak ada user pengirim sistem.');
            return self::FAILURE;
        }

        $today = Carbon::today();
        $sent = 0;

        if ((int) $today->format('d') !== 1) {
            $this->info('Bukan tanggal 1.');
            return self::SUCCESS;
        }

        $employees = User::role('Karyawan')->get();
        $adminDetails = [];
        foreach ($employees as $employee) {
            $employee->loadMissing('location');
            if (!$this->shouldSendForLocation($employee->location, 'notify_leave_monthly_reminder_time')) {
                continue;
            }
            $message = 'Pengingat awal bulan: Silakan ajukan cuti bulanan jika diperlukan.';
            if ($this->sendOncePerDay($senderId, $employee->id, $message, $today)) {
                $sent++;
            }
            if ($employee->location_id) {
                $adminDetails[$employee->location_id] = $adminDetails[$employee->location_id] ?? [];
                $adminDetails[$employee->location_id][] = $employee->name ?? '-';
            }
        }

        $admins = User::role('Admin Lokasi')->with('location')->get();
        foreach ($admins as $admin) {
            if (!$this->shouldSendForLocation($admin->location, 'notify_leave_monthly_reminder_time')) {
                continue;
            }
            $names = $adminDetails[$admin->location_id] ?? [];
            $detail = !empty($names) ? ' Rincian karyawan: ' . $this->formatNameList($names) . '.' : '';
            $message = 'Pengingat awal bulan: Informasikan karyawan untuk pengajuan cuti bulan ini.' . $detail;
            if ($this->sendOncePerDay($senderId, $admin->id, $message, $today)) {
                $sent++;
            }
        }

        $this->info('Leave monthly reminder notifications sent: ' . $sent);
        return self::SUCCESS;
    }

    private function resolveSenderId(): ?int
    {
        $sender = User::role('Super Admin')->orderBy('id')->first()
            ?? User::orderBy('id')->first();
        return $sender?->id;
    }

    private function shouldSendForLocation($location, string $settingKey): bool
    {
        $timezone = $location?->timezone ?? config('app.timezone', 'UTC');
        $now = Carbon::now($timezone);
        $configured = $location?->getSetting($settingKey, '00:00') ?? '00:00';
        $configured = $this->normalizeTime($configured);
        return $configured === $now->format('H:i');
    }

    private function normalizeTime(string $value): string
    {
        if (preg_match('/^([01]\\d|2[0-3]):([0-5]\\d)$/', $value)) {
            return $value;
        }
        return '00:00';
    }

    private function formatNameList(array $names): string
    {
        $names = array_values(array_unique(array_filter($names)));
        $total = count($names);
        $slice = array_slice($names, 0, 10);
        $text = implode(', ', $slice);
        if ($total > count($slice)) {
            $text .= ' dan ' . ($total - count($slice)) . ' lainnya';
        }
        return $text;
    }

    private function sendOncePerDay(int $senderId, int $receiverId, string $message, Carbon $date): bool
    {
        $exists = Message::where('sender_id', $senderId)
            ->where('receiver_id', $receiverId)
            ->where('message', $message)
            ->whereDate('created_at', $date->toDateString())
            ->exists();
        if ($exists) {
            return false;
        }

        Message::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => $message,
        ]);
        return true;
    }
}
