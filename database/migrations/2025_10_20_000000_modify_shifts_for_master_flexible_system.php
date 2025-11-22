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
        Schema::table('shifts', function (Blueprint $table) {
            // Add shift type: 'multiple' for version 1, 'single' for version 2
            $table->enum('shift_type', ['multiple', 'single'])->default('single')->after('day');

            // For multiple shifts: store multiple time slots as JSON
            // For single shifts: store single time slot as JSON
            $table->json('time_slots')->nullable()->after('shift_type');

            // Remove old columns that are no longer needed
            $table->dropColumn(['start_time', 'end_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            // Add back old columns
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            // Remove new columns
            $table->dropColumn(['shift_type', 'time_slots']);
        });
    }
};
