<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee_tasks', function (Blueprint $table) {
            $table->unsignedTinyInteger('progress')->default(0)->after('status');
        });

        Schema::table('master_tasks', function (Blueprint $table) {
            $table->unsignedTinyInteger('progress')->default(0)->after('status');
        });

        Schema::create('task_progress_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('employee_tasks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('progress');
            $table->string('photo_path')->nullable();
            $table->string('document_path')->nullable();
            $table->text('note')->nullable();
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('approval_level', ['none', 'location_admin', 'super_admin'])->default('location_admin');
            $table->boolean('requires_approval')->default(true);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['approval_status', 'approval_level']);
        });

        // Seed initial progress values based on existing status for backward compatibility
        DB::table('employee_tasks')
            ->where('status', 'completed')
            ->update(['progress' => 100]);
        DB::table('employee_tasks')
            ->where('status', 'in_progress')
            ->where('progress', 0)
            ->update(['progress' => 50]);

        DB::table('master_tasks')
            ->where('status', 'completed')
            ->update(['progress' => 100]);
        DB::table('master_tasks')
            ->where('status', 'in_progress')
            ->where('progress', 0)
            ->update(['progress' => 50]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_progress_updates');

        Schema::table('employee_tasks', function (Blueprint $table) {
            $table->dropColumn('progress');
        });

        Schema::table('master_tasks', function (Blueprint $table) {
            $table->dropColumn('progress');
        });
    }
};
