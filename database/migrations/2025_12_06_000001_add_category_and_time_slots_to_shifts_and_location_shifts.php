<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('shifts', 'category')) {
            Schema::table('shifts', function (Blueprint $table) {
                $table->enum('category', ['office', 'non_office'])->default('office')->after('code');
            });
        }

        Schema::table('location_shifts', function (Blueprint $table) {
            if (!Schema::hasColumn('location_shifts', 'category')) {
                $table->enum('category', ['office', 'non_office'])->default('office')->after('shift_id');
            }
            if (!Schema::hasColumn('location_shifts', 'time_slots')) {
                $table->json('time_slots')->nullable()->after('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('location_shifts', function (Blueprint $table) {
            if (Schema::hasColumn('location_shifts', 'time_slots')) {
                $table->dropColumn('time_slots');
            }
            if (Schema::hasColumn('location_shifts', 'category')) {
                $table->dropColumn('category');
            }
        });

        if (Schema::hasColumn('shifts', 'category')) {
            Schema::table('shifts', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }
};
