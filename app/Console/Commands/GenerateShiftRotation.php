<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Location;
use App\Models\User;
use App\Models\ShiftAssignment;
use App\Models\LocationShift;
use Carbon\Carbon;

class GenerateShiftRotation extends Command
{
    protected $signature = 'shifts:rotate {location_id} {--days=14}';
    protected $description = 'Generate rotating shift assignments for a location for the next N days';

    public function handle(): int
    {
        $locationId = (int) $this->argument('location_id');
        $days = (int) $this->option('days');

        $location = Location::find($locationId);
        if (!$location) {
            $this->error('Location not found');
            return self::FAILURE;
        }

        $users = User::role('Employee')->where('location_id', $locationId)->orderBy('id')->get();
        if ($users->isEmpty()) {
            $this->warn('No employees in this location.');
            return self::SUCCESS;
        }

        $locationShifts = $location->shifts()
            ->withPivot(['id', 'time_slots'])
            ->active()
            ->orderBy('location_shifts.id')
            ->get()
            ->map(function ($shift) {
                $shift->pivot_model = $shift->pivot;
                return $shift;
            });

        if ($locationShifts->isEmpty()) {
            $this->warn('No active shifts linked to this location.');
            return self::SUCCESS;
        }

        $today = Carbon::today($location->timezone ?? 'Asia/Jakarta');
        for ($d = 0; $d < $days; $d++) {
            $date = $today->copy()->addDays($d)->toDateString();
            foreach ($users as $idx => $u) {
                $shift = $locationShifts[($idx + $d) % $locationShifts->count()];
                $pivot = $shift->pivot_model;

                if (!$pivot || !$pivot->id) {
                    $this->warn("Skip user {$u->id} on {$date}: missing pivot for shift {$shift->id}");
                    continue;
                }

                if (!ShiftAssignment::where('user_id', $u->id)->whereDate('date', $date)->exists()) {
                    ShiftAssignment::create([
                        'user_id' => $u->id,
                        'location_id' => $location->id,
                        'shift_id' => $shift->id,
                        'location_shift_id' => $pivot->id,
                        'date' => $date,
                        'status' => 'scheduled',
                        'notes' => 'Generated rotation',
                    ]);
                }
            }
        }
        $this->info('Rotation generated.');
        return self::SUCCESS;
    }
}
