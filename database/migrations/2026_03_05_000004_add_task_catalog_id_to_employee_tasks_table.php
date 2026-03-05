<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_tasks', 'task_catalog_id')) {
                $table->unsignedBigInteger('task_catalog_id')->nullable()->after('description');
                $table->foreign('task_catalog_id')->references('id')->on('task_catalogs')->nullOnDelete();
                $table->index('task_catalog_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('employee_tasks', 'task_catalog_id')) {
                $table->dropForeign(['task_catalog_id']);
                $table->dropIndex(['task_catalog_id']);
                $table->dropColumn('task_catalog_id');
            }
        });
    }
};
