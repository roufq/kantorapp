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
        Schema::create('task_slot_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_slot_id');
            $table->enum('action', ['created', 'updated', 'deleted', 'approved', 'rejected']);
            $table->json('data_before')->nullable();
            $table->json('data_after')->nullable();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->timestamps();

            $table->foreign('task_slot_id')->references('id')->on('task_slots')->onDelete('cascade');
            $table->foreign('actor_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_slot_history');
    }
};
