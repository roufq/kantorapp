<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('type', ['sick','annual','unpaid','other']);
            $table->string('reason')->nullable();
            $table->enum('status', ['pending','approved','rejected'])->default('pending');
            $table->string('attachment_path')->nullable();
            $table->timestamps();
            $table->index(['user_id','start_date','end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_leaves');
    }
};

