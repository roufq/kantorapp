<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->string('brand_name')->nullable()->after('name');
            $table->string('brand_logo_url')->nullable()->after('brand_name');
            $table->string('primary_color', 20)->nullable()->after('timezone');
            $table->string('secondary_color', 20)->nullable()->after('primary_color');
            $table->string('custom_css_url')->nullable()->after('settings');
            $table->string('custom_js_url')->nullable()->after('custom_css_url');
        });
    }

    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['brand_name','brand_logo_url','primary_color','secondary_color','custom_css_url','custom_js_url']);
        });
    }
};

