<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('device_id', 128)->nullable()->after('location');
            $table->string('device_user_agent', 255)->nullable()->after('device_id');
            $table->decimal('gps_accuracy_in', 8, 2)->nullable()->after('device_user_agent');
            $table->decimal('gps_accuracy_out', 8, 2)->nullable()->after('gps_accuracy_in');
            $table->string('check_in_photo_path', 2048)->nullable()->after('gps_accuracy_out');
            $table->string('check_out_photo_path', 2048)->nullable()->after('check_in_photo_path');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'device_id',
                'device_user_agent',
                'gps_accuracy_in',
                'gps_accuracy_out',
                'check_in_photo_path',
                'check_out_photo_path',
            ]);
        });
    }
};
