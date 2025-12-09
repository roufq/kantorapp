<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('location_work_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->unsignedInteger('target_minutes')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['location_id', 'employee_id', 'year', 'month'], 'loc_work_targets_unique');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_work_targets');
    }
};
