<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_offs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('day_of_week', ['sunday','monday','tuesday','wednesday','thursday','friday','saturday']);
            $table->timestamps();
            $table->unique(['location_id','user_id','day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_offs');
    }
};

