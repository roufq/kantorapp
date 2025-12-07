<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('weekly_roster_entries', function (Blueprint $table) {
            $table->unsignedBigInteger('shift_assignment_id')->nullable()->after('weekly_roster_id');
            $table->foreign('shift_assignment_id')->references('id')->on('shift_assignments')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('weekly_roster_entries', function (Blueprint $table) {
            $table->dropForeign(['shift_assignment_id']);
            $table->dropColumn('shift_assignment_id');
        });
    }
};
