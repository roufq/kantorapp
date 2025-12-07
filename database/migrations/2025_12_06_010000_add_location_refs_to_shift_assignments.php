<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shift_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('shift_assignments', 'location_id')) {
                $table->foreignId('location_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained()
                    ->nullOnDelete();
            }
            if (!Schema::hasColumn('shift_assignments', 'location_shift_id')) {
                $table->foreignId('location_shift_id')
                    ->nullable()
                    ->after('shift_id')
                    ->constrained('location_shifts')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('shift_assignments', 'location_id')) {
                $table->index(['location_id', 'date'], 'shift_assignments_location_date_idx');
            }
        });

        // Backfill location_id and location_shift_id using existing user/shift data
        if (Schema::hasColumn('shift_assignments', 'location_id')) {
            DB::table('shift_assignments')->orderBy('id')->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    $locationId = DB::table('users')->where('id', $row->user_id)->value('location_id');
                    $locationShiftId = null;
                    if ($locationId) {
                        $locationShiftId = DB::table('location_shifts')
                            ->where('location_id', $locationId)
                            ->where('shift_id', $row->shift_id)
                            ->value('id');
                    }

                    DB::table('shift_assignments')->where('id', $row->id)->update([
                        'location_id' => $locationId,
                        'location_shift_id' => $locationShiftId,
                    ]);
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('shift_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('shift_assignments', 'location_shift_id')) {
                $table->dropForeign(['location_shift_id']);
                $table->dropColumn('location_shift_id');
            }
            if (Schema::hasColumn('shift_assignments', 'location_id')) {
                $table->dropForeign(['location_id']);
                $table->dropIndex('shift_assignments_location_date_idx');
                $table->dropColumn('location_id');
            }
        });
    }
};
