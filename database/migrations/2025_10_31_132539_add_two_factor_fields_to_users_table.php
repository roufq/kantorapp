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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->nullable()->after('email');
            $table->string('two_factor_secret')->nullable()->after('phone_number');
            $table->string('two_factor_method')->nullable()->after('two_factor_secret'); // sms, email, app
            $table->boolean('two_factor_enabled')->default(false)->after('two_factor_method');
            $table->json('two_factor_backup_codes')->nullable()->after('two_factor_enabled');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_backup_codes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone_number',
                'two_factor_secret',
                'two_factor_method',
                'two_factor_enabled',
                'two_factor_backup_codes',
                'two_factor_confirmed_at'
            ]);
        });
    }
};
