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
        Schema::create('task_slot_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_slot_id');
            $table->enum('type', ['photo', 'document', 'link']);
            $table->string('path_or_url');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();

            $table->foreign('task_slot_id')->references('id')->on('task_slots')->onDelete('cascade');
            $table->foreign('uploaded_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_slot_attachments');
    }
};
