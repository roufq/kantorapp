<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create roles
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $adminLokasiRole = Role::create(['name' => 'Admin Lokasi']);
        $karyawanRole = Role::create(['name' => 'Karyawan']);

        // Find users who were previously 'master' admins
        // Note: This relies on the old 'role' column still being in the database for now.
        // It's better to run this seeder BEFORE removing the column.
        $masterUsers = User::where('role', 'master')->get();

        foreach ($masterUsers as $user) {
            $user->assignRole($superAdminRole);
        }

        // Assign 'Karyawan' role to all other users for now
        $otherUsers = User::where('role', '!=', 'master')->orWhereNull('role')->get();
        foreach ($otherUsers as $user) {
            $user->assignRole($karyawanRole);
        }
    }
}