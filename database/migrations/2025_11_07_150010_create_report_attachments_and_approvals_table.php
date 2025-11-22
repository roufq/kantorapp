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
        Schema::create('report_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('uploaded_by')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->default(0); // enforce 10 MB limit in validation
            $table->timestamps();
            $table->index('report_id');
        });

        Schema::create('report_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->foreignId('approver_id')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->enum('approver_role', ['admin_lokasi', 'super_admin']);
            $table->unsignedTinyInteger('step_order')->default(1);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('decided_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['report_id', 'approver_role', 'step_order']);
            $table->index(['report_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_approvals');
        Schema::dropIfExists('report_attachments');
    }
};
