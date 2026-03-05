<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_catalogs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jobdesk_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('unit', ['minutes', 'points', 'weight'])->default('points');
            $table->unsignedInteger('value')->default(1);
            $table->enum('task_type', ['routine', 'project'])->default('routine');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('jobdesk_id')->references('id')->on('jobdesks')->onDelete('cascade');
            $table->index(['jobdesk_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_catalogs');
    }
};
