<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill roster entries with shift assignments and link attendances to assignments
        if (!Schema::hasTable('weekly_roster_entries') || !Schema::hasTable('shift_assignments')) {
            return;
        }

        DB::beginTransaction();
        try {
            // Backfill roster entries
            $entries = DB::table('weekly_roster_entries')->get();
            foreach ($entries as $entry) {
                if ($entry->status === 'off') {
                    continue;
                }
                // Fetch roster + location shift to know location/shift
                $roster = DB::table('weekly_rosters')->where('id', $entry->weekly_roster_id)->first();
                if (!$roster) {
                    continue;
                }
                $locationShift = DB::table('location_shifts')->where('id', $roster->location_shift_id)->first();
                if (!$locationShift) {
                    continue;
                }
                $assignment = DB::table('shift_assignments')->where([
                    ['user_id', '=', $entry->user_id],
                    ['date', '=', $entry->date],
                    ['location_id', '=', $locationShift->location_id],
                    ['location_shift_id', '=', $locationShift->id],
                ])->first();

                if (!$assignment) {
                    $assignmentId = DB::table('shift_assignments')->insertGetId([
                        'user_id' => $entry->user_id,
                        'location_id' => $locationShift->location_id,
                        'shift_id' => $locationShift->shift_id,
                        'location_shift_id' => $locationShift->id,
                        'date' => $entry->date,
                        'status' => 'scheduled',
                        'notes' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $assignmentId = $assignment->id;
                }

                DB::table('weekly_roster_entries')
                    ->where('id', $entry->id)
                    ->update(['shift_assignment_id' => $assignmentId]);
            }

            // Backfill attendances
            if (Schema::hasColumn('attendances', 'shift_assignment_id')) {
                $attendances = DB::table('attendances')->whereNull('shift_assignment_id')->get();
                foreach ($attendances as $att) {
                    $date = Carbon::parse($att->check_in_time)->toDateString();
                    $assignment = DB::table('shift_assignments')
                        ->where('user_id', $att->user_id)
                        ->whereDate('date', $date)
                        ->when($att->location_id, function ($q) use ($att) {
                            $q->where('location_id', $att->location_id);
                        })
                        ->orderByDesc('id')
                        ->first();
                    if ($assignment) {
                        DB::table('attendances')->where('id', $att->id)->update([
                            'shift_assignment_id' => $assignment->id,
                            'shift_id' => $assignment->shift_id,
                        ]);
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function down(): void
    {
        // no data rollback
    }
};