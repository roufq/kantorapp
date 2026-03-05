<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('approval_rules', function (Blueprint $table) {
            $table->id();
            $table->string('scope')->default('task');
            $table->string('department')->nullable();
            $table->unsignedInteger('min_value')->default(0);
            $table->enum('approval_level', ['location_admin', 'super_admin'])->default('location_admin');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['scope', 'department', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_rules');
    }
};
