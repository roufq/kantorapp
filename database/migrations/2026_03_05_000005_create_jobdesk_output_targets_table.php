<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobdesk_output_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jobdesk_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->enum('unit', ['minutes', 'points', 'weight'])->default('points');
            $table->unsignedInteger('target_value')->default(0);
            $table->timestamps();

            $table->foreign('jobdesk_id')->references('id')->on('jobdesks')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->nullOnDelete();
            $table->unique(['jobdesk_id', 'employee_id', 'year', 'month'], 'jobdesk_targets_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobdesk_output_targets');
    }
};
