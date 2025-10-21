<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserRoleBackfillSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure roles exist
        Role::firstOrCreate(['name' => 'Super Admin']);
        Role::firstOrCreate(['name' => 'Admin Lokasi']);
        Role::firstOrCreate(['name' => 'Karyawan']);

        // Promote a Super Admin if none exists
        if (User::role('Super Admin')->count() === 0) {
            $candidate = User::orderBy('id')->first();
            if ($candidate) {
                $candidate->assignRole('Super Admin');
            }
        }

        // Assign Karyawan to users without any role
        User::doesntHave('roles')->get()->each(function (User $user) {
            $user->assignRole('Karyawan');
        });
    }
}

