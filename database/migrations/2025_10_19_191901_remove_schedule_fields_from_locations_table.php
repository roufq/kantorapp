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
        $isSqlite = Schema::getConnection()->getDriverName() === 'sqlite';
        if (!$isSqlite) {
            Schema::table('locations', function (Blueprint $table) {
                if (Schema::hasColumn('locations', 'schedule_type')) {
                    $table->dropColumn(['schedule_type', 'daily_schedule']);
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('locations', 'schedule_type')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->string('schedule_type')->nullable()->after('shift_enabled');
                $table->json('daily_schedule')->nullable()->after('schedule_type');
            });
        }
    }
};
