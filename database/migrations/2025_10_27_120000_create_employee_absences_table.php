<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_absences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('cascade');
            $table->date('date');
            $table->string('type')->default('alfa');
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->unique(['user_id','date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_absences');
    }
};

