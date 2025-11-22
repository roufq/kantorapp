<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Attendance;
use App\Models\EmployeeAbsence;
use App\Services\WorkdayService;
use Carbon\Carbon;

class MarkDailyAbsences extends Command
{
    protected $signature = 'attendance:mark-absences {date? : Tanggal (Y-m-d), default: hari ini}';
    protected $description = 'Tandai alfa otomatis untuk user yang tidak absen pada hari kerja';

    public function handle(): int
    {
        $dateStr = $this->argument('date') ?: Carbon::now('Asia/Jakarta')->toDateString();
        try {
            $date = Carbon::parse($dateStr, 'Asia/Jakarta');
        } catch (\Throwable $e) {
            $this->error('Format tanggal tidak valid. Gunakan Y-m-d');
            return static::FAILURE;
        }

        $count = 0; $skipped = 0;
        User::chunk(500, function($users) use ($date, &$count, &$skipped){
            foreach ($users as $user) {
                // Hari kerja? (weekly off, libur, izin disetujui akan dikembalikan false)
                if (!WorkdayService::isWorkingDay($user, $date)) { $skipped++; continue; }

                // Sudah ada attendance pada hari itu?
                $hasAttendance = Attendance::where('user_id', $user->id)
                    ->whereDate('check_in_time', $date->toDateString())
                    ->exists();
                if ($hasAttendance) { $skipped++; continue; }

                // Sudah ditandai alfa?
                $exists = EmployeeAbsence::where('user_id', $user->id)
                    ->whereDate('date', $date->toDateString())
                    ->exists();
                if ($exists) { $skipped++; continue; }

                EmployeeAbsence::create([
                    'user_id' => $user->id,
                    'location_id' => $user->location_id,
                    'date' => $date->toDateString(),
                    'type' => 'alfa',
                    'notes' => 'Auto-marked by system',
                ]);
                $count++;
            }
        });

        $this->info("Tanggal {$date->toDateString()}: {$count} alfa dibuat, {$skipped} dilewati.");
        return static::SUCCESS;
    }
}

