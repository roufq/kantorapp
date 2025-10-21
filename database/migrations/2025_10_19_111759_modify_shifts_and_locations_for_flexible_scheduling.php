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
        // Drop location_id from shifts table
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn('location_id');
        });

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
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['schedule_type', 'daily_schedule']);
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
        });
    }
};
