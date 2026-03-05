<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create roles if they don't exist
        Role::firstOrCreate(['name' => 'Super Admin']);
        Role::firstOrCreate(['name' => 'Admin Lokasi']);
        Role::firstOrCreate(['name' => 'HR']);
        Role::firstOrCreate(['name' => 'Karyawan']);
    }
}
