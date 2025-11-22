<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->unsignedInteger('break_minutes')->nullable()->after('description');
        });

        Schema::table('shift_assignments', function (Blueprint $table) {
            $table->boolean('handover_required')->default(false)->after('status');
            $table->text('handover_note')->nullable()->after('handover_required');
        });
    }

    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropColumn('break_minutes');
        });
        Schema::table('shift_assignments', function (Blueprint $table) {
            $table->dropColumn(['handover_required','handover_note']);
        });
    }
};

