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
        // cleanup if tables already exist from previous attempts
        Schema::dropIfExists('weekly_roster_entries');
        Schema::dropIfExists('weekly_rosters');

        Schema::create('weekly_rosters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('location_shift_id');
            $table->date('week_start');
            $table->date('week_end');
            $table->boolean('locked')->default(false);
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->unique(['location_id', 'week_start'], 'wr_loc_week_unique');
        });

        Schema::create('weekly_roster_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('weekly_roster_id');
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->unsignedInteger('slot_index')->default(0);
            $table->enum('status', ['scheduled', 'off'])->default('scheduled');
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->unique(['weekly_roster_id', 'user_id', 'date', 'slot_index'], 'wre_roster_user_date_slot_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_roster_entries');
        Schema::dropIfExists('weekly_rosters');
    }
};
