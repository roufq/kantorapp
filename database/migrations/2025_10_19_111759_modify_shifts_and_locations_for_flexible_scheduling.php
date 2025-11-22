<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite (tests), skip destructive column drops to avoid driver limitations
        $isSqlite = Schema::getConnection()->getDriverName() === 'sqlite';
        if (!$isSqlite) {
            // Drop location_id from shifts table (MySQL/Postgres supported)
            Schema::table('shifts', function (Blueprint $table) {
                if (Schema::hasColumn('shifts', 'location_id')) {
                    try { $table->dropForeign(['location_id']); } catch (\Throwable $e) { /* ignore */ }
                    $table->dropColumn('location_id');
                }
            });
        }

        // Add schedule_type and daily_schedule to locations table
        Schema::table('locations', function (Blueprint $table) {
            $table->enum('schedule_type', ['daily', 'shifts'])->default('daily')->after('shift_enabled');
            $table->json('daily_schedule')->nullable()->after('schedule_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the changes
        $isSqlite = Schema::getConnection()->getDriverName() === 'sqlite';
        if (!$isSqlite) {
            Schema::table('locations', function (Blueprint $table) {
                if (Schema::hasColumn('locations', 'schedule_type')) {
                    $table->dropColumn(['schedule_type', 'daily_schedule']);
                }
            });
        }

        if (!$isSqlite) {
            Schema::table('shifts', function (Blueprint $table) {
                if (!Schema::hasColumn('shifts', 'location_id')) {
                    $table->foreignId('location_id')->constrained()->onDelete('cascade');
                }
            });
        }
    }
};
