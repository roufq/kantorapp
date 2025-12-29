<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('overtime_approvals', function (Blueprint $table) {
            $table->unsignedTinyInteger('level')->default(1)->after('master_id');
        });
    }

    public function down(): void
    {
        Schema::table('overtime_approvals', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
};
