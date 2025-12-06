<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('location_shifts', function (Blueprint $table) {
            if (!Schema::hasColumn('location_shifts', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('time_slots');
            }
        });
    }

    public function down(): void
    {
        Schema::table('location_shifts', function (Blueprint $table) {
            if (Schema::hasColumn('location_shifts', 'is_default')) {
                $table->dropColumn('is_default');
            }
        });
    }
};
