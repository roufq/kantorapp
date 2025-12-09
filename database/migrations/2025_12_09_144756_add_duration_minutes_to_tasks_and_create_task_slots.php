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
        // Tambah durasi menit ke tasks (employee_tasks)
        Schema::table('employee_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_tasks', 'duration_minutes')) {
                $table->integer('duration_minutes')->nullable()->after('due_date');
            }
        });

        Schema::create('task_slots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->string('name');
            $table->unsignedInteger('percentage'); // 0-100
            $table->unsignedInteger('minutes'); // alokasi menit
            $table->unsignedTinyInteger('order')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('task_id')->references('id')->on('employee_tasks')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_slots');

        Schema::table('employee_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('employee_tasks', 'duration_minutes')) {
                $table->dropColumn('duration_minutes');
            }
        });
    }
};
