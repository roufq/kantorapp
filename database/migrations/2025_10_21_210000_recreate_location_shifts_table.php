<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('location_shifts')) {
            Schema::create('location_shifts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('location_id')->constrained()->onDelete('cascade');
                $table->foreignId('shift_id')->constrained()->onDelete('cascade');
                $table->timestamps();
                $table->unique(['location_id', 'shift_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('location_shifts');
    }
};

