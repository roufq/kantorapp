<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\EmployeeWorkRecap;
use App\Models\TaskSlot;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateWorkRecaps extends Command
{
    protected $signature = 'work-recaps:generate {--year=} {--month=} {--location=} {--employee=}';

    protected $description = 'Hitung rekap jam kerja bulanan dari slot tugas yang approved dan kehadiran (jika ada).';

    public function handle(): int
    {
        $year = (int) ($this->option('year') ?: now()->year);
        $month = (int) ($this->option('month') ?: now()->month);
        $locationFilter = $this->option('location');
        $employeeFilter = $this->option('employee');

        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = (clone $start)->endOfMonth();

        $slotMinutes = $this->collectSlotMinutes($start, $end, $locationFilter, $employeeFilter);
        $attendanceMinutes = $this->collectAttendanceMinutes($start, $end, $locationFilter, $employeeFilter);

        $keys = collect(array_keys($slotMinutes))
            ->merge(array_keys($attendanceMinutes))
            ->unique();

        $updated = 0;
        foreach ($keys as $key) {
            [$employeeId, $locationId] = explode('|', $key);
            $slotMin = $slotMinutes[$key] ?? 0;
            $attMin = $attendanceMinutes[$key] ?? 0;
            $total = $slotMin + $attMin;

            EmployeeWorkRecap::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'location_id' => $locationId ?: null,
                    'year' => $year,
                    'month' => $month,
                ],
                [
                    'slot_minutes_approved' => $slotMin,
                    'attendance_minutes' => $attMin,
                    'total_minutes' => $total,
                ]
            );
            $updated++;
        }

        $this->info("Recap generated for {$updated} employee/location rows ({$month}-{$year}).");
        return Command::SUCCESS;
    }

    private function makeKey(?int $employeeId, ?int $locationId): ?string
    {
        if (!$employeeId) {
            return null;
        }
        return $employeeId . '|' . ($locationId ?: '');
    }

    private function collectSlotMinutes(Carbon $start, Carbon $end, $locationFilter, $employeeFilter): array
    {
        $query = TaskSlot::with('task.assignee')
            ->where('status', 'approved')
            ->whereBetween('approved_at', [$start, $end]);

        if ($locationFilter) {
            $query->whereHas('task.assignee', function ($q) use ($locationFilter) {
                $q->where('location_id', $locationFilter);
            });
        }

        if ($employeeFilter) {
            $query->whereHas('task.assignee', function ($q) use ($employeeFilter) {
                $q->where('employee_id', $employeeFilter)
                    ->orWhere('karyawan_id', $employeeFilter);
            });
        }

        $minutes = [];
        foreach ($query->get() as $slot) {
            $user = $slot->task?->assignee;
            if (!$user) {
                continue;
            }
            $employeeId = $user->employee_id ?? $user->karyawan_id;
            $locationId = $user->location_id;
            $key = $this->makeKey($employeeId, $locationId);
            if (!$key) {
                continue;
            }
            $minutes[$key] = ($minutes[$key] ?? 0) + (int) $slot->minutes;
        }

        return $minutes;
    }

    private function collectAttendanceMinutes(Carbon $start, Carbon $end, $locationFilter, $employeeFilter): array
    {
        $query = Attendance::with('user')
            ->whereNotNull('check_in_time')
            ->whereNotNull('check_out_time')
            ->whereBetween('check_in_time', [$start, $end]);

        if ($locationFilter) {
            $query->where('location_id', $locationFilter);
        }

        if ($employeeFilter) {
            $query->whereHas('user', function ($q) use ($employeeFilter) {
                $q->where('employee_id', $employeeFilter)
                    ->orWhere('karyawan_id', $employeeFilter);
            });
        }

        $minutes = [];
        foreach ($query->get() as $att) {
            $user = $att->user;
            if (!$user) {
                continue;
            }
            $employeeId = $user->employee_id ?? $user->karyawan_id;
            $locationId = $att->location_id ?: $user->location_id;
            $key = $this->makeKey($employeeId, $locationId);
            if (!$key) {
                continue;
            }

            $diff = $att->check_out_time->diffInMinutes($att->check_in_time);
            $minutes[$key] = ($minutes[$key] ?? 0) + $diff;
        }

        return $minutes;
    }
}
