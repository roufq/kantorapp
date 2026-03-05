<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_jobdesk_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('jobdesk_id');
            $table->boolean('is_primary')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('jobdesk_id')->references('id')->on('jobdesks')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->index(['employee_id', 'jobdesk_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_jobdesk_assignments');
    }
};
