<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('shifts')) {
            return;
        }

        // Attempt to drop problematic index that may reference removed column
        try {
            $driver = DB::getDriverName();
            if ($driver === 'sqlite') {
                DB::statement('DROP INDEX IF EXISTS shifts_location_id_is_active_index');
            } elseif ($driver === 'mysql') {
                // MySQL requires ON <table>
                DB::statement('DROP INDEX shifts_location_id_is_active_index ON shifts');
            } elseif ($driver === 'pgsql') {
                DB::statement('DROP INDEX IF EXISTS shifts_location_id_is_active_index');
            }
        } catch (\Throwable $e) {
            // ignore if index does not exist
        }

        // Ensure location_id column is removed safely if still present
        if (Schema::hasColumn('shifts', 'location_id')) {
            try {
                Schema::table('shifts', function (Blueprint $table) {
                    try { $table->dropForeign(['location_id']); } catch (\Throwable $e) {}
                });
            } catch (\Throwable $e) {
                // ignore foreign key drop issues
            }

            try {
                Schema::table('shifts', function (Blueprint $table) {
                    $table->dropColumn('location_id');
                });
            } catch (\Throwable $e) {
                // ignore if SQLite cannot alter, this migration runs after previous rebuilds
            }
        }
    }

    public function down(): void
    {
        // No-op: we don't re-create legacy index/column
    }
};
