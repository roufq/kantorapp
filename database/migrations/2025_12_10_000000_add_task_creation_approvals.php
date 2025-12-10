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
        Schema::table('employee_tasks', function (Blueprint $table) {
            $table->boolean('requires_approval')->default(false)->after('progress');
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('approved')->after('requires_approval');
            $table->enum('approval_level', ['none', 'location_admin', 'super_admin'])->default('none')->after('approval_status');
            $table->foreignId('approved_by')->nullable()->after('approval_level')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('approval_note')->nullable()->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_tasks', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn([
                'requires_approval',
                'approval_status',
                'approval_level',
                'approved_at',
                'approval_note',
            ]);
        });
    }
};
