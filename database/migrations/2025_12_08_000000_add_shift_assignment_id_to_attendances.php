<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'shift_assignment_id')) {
                $table->unsignedBigInteger('shift_assignment_id')->nullable()->after('shift_id');
                $table->foreign('shift_assignment_id')->references('id')->on('shift_assignments')->cascadeOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'shift_assignment_id')) {
                $table->dropForeign(['shift_assignment_id']);
                $table->dropColumn('shift_assignment_id');
            }
        });
    }
};