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
        Schema::table('employees', function (Blueprint $table) {
            $table->string('schedule_type')->default('fixed')->after('location_id');
            $table->foreignId('default_shift_id')->nullable()->after('schedule_type')->constrained('shifts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['default_shift_id']);
            $table->dropColumn(['schedule_type', 'default_shift_id']);
        });
    }
};