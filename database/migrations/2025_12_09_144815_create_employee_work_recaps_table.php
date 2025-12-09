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
        Schema::create('employee_work_recaps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedInteger('slot_minutes_approved')->default(0);
            $table->unsignedInteger('attendance_minutes')->default(0);
            $table->unsignedInteger('total_minutes')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'location_id', 'year', 'month'], 'work_recaps_emp_loc_month_unique');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('location_id')->references('id')->on('locations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_work_recaps');
    }
};
