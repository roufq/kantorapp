<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename roles in roles table
        DB::table('roles')->where('name', 'Employee')->update(['name' => 'Employee']);
        DB::table('roles')->where('name', 'Location Admin')->update(['name' => 'Location Admin']);
        
        // Update any model_has_roles or role_has_permissions if they use names (usually they use IDs, so we are safe)
        
        // Clear Spatie permission cache
        try {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (\Exception $e) {
            // Might fail if Spatie is not fully loaded in migration context
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')->where('name', 'Employee')->update(['name' => 'Employee']);
        DB::table('roles')->where('name', 'Location Admin')->update(['name' => 'Location Admin']);
        
        try {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (\Exception $e) {
        }
    }
};
