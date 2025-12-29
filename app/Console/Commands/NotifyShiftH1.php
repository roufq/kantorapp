<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\ShiftAssignment;
use App\Models\User;
use App\Models\WeeklyRosterEntry;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyShiftH1 extends Command
{
    protected $signature = 'notify:shift-h1';
    protected $description = 'Kirim reminder jadwal masuk H-1 via pesan in-app.';

    public function handle(): int
    {
        $senderId = $this->resolveSenderId();
        if (!$senderId) {
            $this->error('Tidak ada user pengirim sistem.');
            return self::FAILURE;
        }

        $tomorrow = Carbon::tomorrow();
        $rosterEntries = WeeklyRosterEntry::whereDate('date', $tomorrow->toDateString())
            ->get()
            ->keyBy('user_id');
        $offUsers = $rosterEntries->filter(fn($e) => $e->status === 'off')->keys()->all();

        $assignments = ShiftAssignment::with(['user', 'locationShift.location', 'shift', 'location'])
            ->whereDate('date', $tomorrow->toDateString())
            ->where('status', 'scheduled')
            ->get();

        $adminUsers = User::role('Admin Lokasi')->with('location')->get();
        $adminsByLocation = $adminUsers->groupBy('location_id');
        $adminNames = [];
        $sent = 0;

        foreach ($assignments as $assignment) {
            if (in_array($assignment->user_id, $offUsers, true)) {
                continue;
            }
            $user = $assignment->user;
            if (!$user) {
                continue;
            }
            $location = $assignment->location ?? $assignment->locationShift?->location ?? $user->location;
            if (!$this->shouldSendForLocation($location, 'notify_shift_h1_time')) {
                continue;
            }

            $timeRange = $this->formatAssignmentTime($assignment, $tomorrow);
            $locationName = $assignment->location?->name ?? $assignment->locationShift?->location?->name;
            $dateLabel = $tomorrow->format('d M Y');
            $message = 'Pengingat jadwal: Besok (' . $dateLabel . ') Anda dijadwalkan masuk'
                . ($timeRange ? ' pukul ' . $timeRange : '')
                . ($locationName ? ' di ' . $locationName : '')
                . '.';

            if ($this->sendOncePerDay($senderId, $user->id, $message, Carbon::today())) {
                $sent++;
            }

            foreach ($adminsByLocation[$user->location_id] ?? [] as $admin) {
                $adminNames[$admin->id] = $adminNames[$admin->id] ?? [];
                $adminNames[$admin->id][] = $user->name ?? '-';
            }
        }

        foreach ($adminNames as $adminId => $names) {
            $admin = $adminUsers->firstWhere('id', $adminId);
            if (!$admin || !$this->shouldSendForLocation($admin->location, 'notify_shift_h1_time')) {
                continue;
            }
            $locName = $admin?->location?->name;
            $dateLabel = $tomorrow->format('d M Y');
            $count = count(array_unique($names));
            $message = 'Pengingat jadwal: Besok (' . $dateLabel . ') ada ' . $count . ' jadwal masuk'
                . ($locName ? ' di lokasi ' . $locName : '')
                . '. Rincian karyawan: ' . $this->formatNameList($names) . '.';
            if ($this->sendOncePerDay($senderId, $adminId, $message, Carbon::today())) {
                $sent++;
            }
        }

        $this->info('Shift H-1 notifications sent: ' . $sent);
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

    private function formatAssignmentTime(ShiftAssignment $assignment, Carbon $date): ?string
    {
        $pivot = $assignment->locationShift;
        $intervals = $pivot ? $pivot->slotIntervalsForDate($date) : [];

        if (empty($intervals) && $assignment->shift) {
            $fakePivot = new \App\Models\LocationShift([
                'location_id' => $assignment->location_id,
                'shift_id' => $assignment->shift_id,
                'time_slots' => $assignment->shift->time_slots ?? [],
            ]);
            $intervals = $fakePivot->slotIntervalsForDate($date);
        }

        if (empty($intervals)) {
            return null;
        }

        $starts = array_map(fn($slot) => $slot[0], $intervals);
        $ends = array_map(fn($slot) => $slot[1], $intervals);
        usort($starts, fn($a, $b) => $a->gt($b) ? 1 : -1);
        usort($ends, fn($a, $b) => $a->gt($b) ? 1 : -1);
        $start = $starts[0];
        $end = end($ends);

        return $start->format('H:i') . ' - ' . $end->format('H:i');
    }
}
