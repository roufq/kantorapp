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
        if ($isSqlite) {
            // SQLite: cannot drop columns; only add the new column if missing
            if (!Schema::hasColumn('shifts', 'day')) {
                Schema::table('shifts', function (Blueprint $table) {
                    $table->enum('day', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable()->after('code');
                });
            }
        } else {
            Schema::table('shifts', function (Blueprint $table) {
                try { $table->dropColumn(['work_hours', 'break_times']); } catch (\Throwable $e) { /* ignore */ }
                if (!Schema::hasColumn('shifts', 'day')) {
                    $table->enum('day', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable()->after('code');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $isSqlite = Schema::getConnection()->getDriverName() === 'sqlite';
        if (!$isSqlite) {
            Schema::table('shifts', function (Blueprint $table) {
                if (Schema::hasColumn('shifts', 'day')) {
                    $table->dropColumn('day');
                }
                if (!Schema::hasColumn('shifts', 'work_hours')) {
                    $table->integer('work_hours')->default(8);
                }
                if (!Schema::hasColumn('shifts', 'break_times')) {
                    $table->json('break_times')->nullable();
                }
            });
        }
    }
};
