<?php

namespace App\Console\Commands;

use App\Models\EmployeeLeave;
use App\Models\EmployeeLeaveBalance;
use App\Models\Message;
use App\Models\User;
use App\Services\WorkdayService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;

class NotifyLeaveMonthlySummary extends Command
{
    protected $signature = 'notify:leave-monthly-summary';
    protected $description = 'Kirim ringkasan sisa cuti akhir bulan via pesan in-app.';

    public function handle(): int
    {
        $senderId = $this->resolveSenderId();
        if (!$senderId) {
            $this->error('Tidak ada user pengirim sistem.');
            return self::FAILURE;
        }

        $today = Carbon::today();
        if (!$today->isLastOfMonth()) {
            $this->info('Bukan hari terakhir bulan.');
            return self::SUCCESS;
        }
        $year = (int) $today->format('Y');
        $employees = User::role('Karyawan')->with('location')->get();
        $admins = User::role('Admin Lokasi')->with('location')->get()->groupBy('location_id');
        $sent = 0;

        $adminDetails = [];
        foreach ($employees as $employee) {
            $location = $employee->location;
            if (!$this->shouldSendForLocation($location, 'notify_leave_monthly_summary_time')) {
                continue;
            }
            $summary = $this->buildAnnualBalanceSummary($employee, $year);
            $message = 'Info akhir bulan: Sisa cuti tahunan Anda ' . $summary['remaining']
                . ' hari (kuota ' . $summary['quota'] . ' + carry ' . $summary['carry_over']
                . ', terpakai ' . $summary['used'] . ').';

            if ($this->sendOncePerDay($senderId, $employee->id, $message, $today)) {
                $sent++;
            }

            foreach ($admins[$employee->location_id] ?? [] as $admin) {
                $adminDetails[$admin->id] = $adminDetails[$admin->id] ?? [];
                $adminDetails[$admin->id][] = $employee->name . ' (' . $summary['remaining'] . ' hari)';
            }
        }

        foreach ($adminDetails as $adminId => $details) {
            $admin = $admins->flatten()->firstWhere('id', $adminId);
            if (!$admin || !$this->shouldSendForLocation($admin->location, 'notify_leave_monthly_summary_time')) {
                continue;
            }
            $detailText = $this->formatNameList($details);
            $adminMessage = 'Info akhir bulan: Ringkasan sisa cuti karyawan di lokasi '
                . ($admin->location?->name ?? 'Anda')
                . '. Rincian: ' . $detailText . '.';
            if ($this->sendOncePerDay($senderId, $adminId, $adminMessage, $today)) {
                $sent++;
            }
        }

        $this->info('Leave monthly summary notifications sent: ' . $sent);
        return self::SUCCESS;
    }

    private function buildAnnualBalanceSummary(User $user, int $year): array
    {
        $balance = EmployeeLeaveBalance::firstOrCreate(
            ['user_id' => $user->id, 'year' => $year],
            ['annual_quota' => 12, 'carry_over' => 0]
        );

        $used = $this->countApprovedAnnualLeaveDays($user, $year);
        $totalQuota = $balance->annual_quota + $balance->carry_over;
        $remaining = max(0, $totalQuota - $used);

        return [
            'year' => $year,
            'quota' => $balance->annual_quota,
            'carry_over' => $balance->carry_over,
            'used' => $used,
            'remaining' => $remaining,
        ];
    }

    private function countApprovedAnnualLeaveDays(User $user, int $year): int
    {
        $yearStart = Carbon::create($year, 1, 1)->startOfDay();
        $yearEnd = Carbon::create($year, 12, 31)->endOfDay();

        $leaves = EmployeeLeave::where('user_id', $user->id)
            ->where('type', 'annual')
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', $yearEnd->toDateString())
            ->whereDate('end_date', '>=', $yearStart->toDateString())
            ->get();

        $total = 0;
        foreach ($leaves as $leave) {
            $start = Carbon::parse($leave->start_date)->max($yearStart);
            $end = Carbon::parse($leave->end_date)->min($yearEnd);
            $total += $this->countChargeableLeaveDays($user, $start, $end);
        }

        return $total;
    }

    private function countChargeableLeaveDays(User $user, $startDate, $endDate): int
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay();
        if ($start->gt($end)) {
            [$start, $end] = [$end, $start];
        }

        $count = 0;
        foreach (CarbonPeriod::create($start, $end) as $date) {
            if (WorkdayService::isWeeklyOff($user, $date)) {
                continue;
            }
            if (WorkdayService::isHolidayForUser($user, $date)) {
                continue;
            }
            $count++;
        }
        return $count;
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
