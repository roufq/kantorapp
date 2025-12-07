<?php

namespace App\Console\Commands;

use App\Models\ShiftAssignment;
use App\Models\Attendance;
use App\Notifications\ShiftAbsenceAlertNotification;
use App\Notifications\ShiftReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SendShiftReminders extends Command
{
    protected $signature = 'shifts:notify {--window=60} {--grace=5}';
    protected $description = 'Kirim reminder sebelum shift mulai dan alert jika tidak ada kehadiran setelah shift berakhir.';

    public function handle(): int
    {
        $window = (int) $this->option('window'); // minutes before start
        $grace = (int) $this->option('grace');   // minutes after end for absence alert

        $today = Carbon::today();

        $assignments = ShiftAssignment::with(['user', 'shift', 'location', 'locationShift.location'])
            ->whereDate('date', $today->toDateString())
            ->get();

        $reminded = 0;
        $alerted = 0;

        foreach ($assignments as $assignment) {
            $locTz = optional($assignment->location)->timezone ?: config('app.timezone');
            $date = Carbon::parse($assignment->date, $locTz);

            $pivot = $assignment->locationShift;
            $slots = $pivot ? $pivot->slotIntervalsForDate($date) : [];
            if (empty($slots) && $assignment->shift) {
                $fakePivot = new \App\Models\LocationShift([
                    'location_id' => $assignment->location_id,
                    'shift_id' => $assignment->shift_id,
                    'time_slots' => $assignment->shift->time_slots ?? [],
                ]);
                $slots = $fakePivot->slotIntervalsForDate($date);
            }

            if (empty($slots)) {
                continue;
            }

            // Reminder: gunakan slot pertama
            [$start, $end] = $slots[0];
            $now = Carbon::now($locTz);

            $reminderKey = "shift:reminder:{$assignment->id}:{$assignment->date->toDateString()}";
            if ($now->between($start->copy()->subMinutes($window), $start) && Cache::add($reminderKey, true, now()->addDay())) {
                if ($assignment->user && $assignment->user->email) {
                    $assignment->user->notify(new ShiftReminderNotification($assignment, $start->format('H:i')));
                    $reminded++;
                }
            }

            // Alert absence: gunakan slot terakhir
            $endTimes = array_map(fn ($slot) => $slot[1], $slots);
            usort($endTimes, fn ($a, $b) => $a->gt($b) ? 1 : -1);
            $lastEnd = end($endTimes);

            $alertKey = "shift:absence:{$assignment->id}:{$assignment->date->toDateString()}";
            $attendanceExists = Attendance::where('user_id', $assignment->user_id)
                ->whereDate('check_in_time', $assignment->date->toDateString())
                ->exists();

            if (!$attendanceExists && $now->gt($lastEnd->copy()->addMinutes($grace)) && Cache::add($alertKey, true, now()->addDay())) {
                if ($assignment->user && $assignment->user->email) {
                    $assignment->user->notify(new ShiftAbsenceAlertNotification($assignment, $lastEnd->format('H:i')));
                    $alerted++;
                }
            }
        }

        $this->info("Reminders sent: {$reminded}; Alerts sent: {$alerted}");

        return self::SUCCESS;
    }
}
