<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jobdesks', function (Blueprint $table) {
            if (!Schema::hasColumn('jobdesks', 'min_attendance_minutes')) {
                $table->unsignedInteger('min_attendance_minutes')->nullable()->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jobdesks', function (Blueprint $table) {
            if (Schema::hasColumn('jobdesks', 'min_attendance_minutes')) {
                $table->dropColumn('min_attendance_minutes');
            }
        });
    }
};
