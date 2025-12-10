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
        Schema::table('task_slot_attachments', function (Blueprint $table) {
            $table->text('path_or_url')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_slot_attachments', function (Blueprint $table) {
            $table->string('path_or_url')->change();
        });
    }
};
