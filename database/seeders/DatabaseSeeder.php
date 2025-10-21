<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Division;
use App\Models\Location;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(LocationSeeder::class);

        // Create divisions
        Division::create(['nama' => 'HR']);
        Division::create(['nama' => 'IT']);
        Division::create(['nama' => 'Finance']);

        // Create employees (attach to random locations)
        Employee::create([
            'nama' => 'John Doe',
            'email' => 'john@example.com',
            'telepon' => '123456789',
            'alamat' => 'Address 1',
            'jabatan' => 'Manager',
            'departemen' => 'HR',
            'tanggal_lahir' => '1990-01-01',
            'divisi_id' => 1,
            'location_id' => Location::inRandomOrder()->value('id'),
        ]);
        Employee::create([
            'nama' => 'Jane Smith',
            'email' => 'jane@example.com',
            'telepon' => '987654321',
            'alamat' => 'Address 2',
            'jabatan' => 'Developer',
            'departemen' => 'IT',
            'tanggal_lahir' => '1992-05-15',
            'divisi_id' => 2,
            'location_id' => Location::inRandomOrder()->value('id'),
        ]);

        // Create per-location employees and linked users
        foreach (Location::all() as $loc) {
            for ($i = 1; $i <= 3; $i++) {
                $empName = $loc->code . ' Employee ' . $i;
                $empEmail = strtolower($loc->code) . '.employee' . $i . '@example.com';
                $employee = Employee::create([
                    'nama' => $empName,
                    'email' => $empEmail,
                    'telepon' => '08' . rand(100000000, 999999999),
                    'alamat' => $loc->address,
                    'jabatan' => 'Staff',
                    'departemen' => 'General',
                    'tanggal_lahir' => '1995-01-01',
                    'divisi_id' => Division::inRandomOrder()->value('id'),
                    'location_id' => $loc->id,
                ]);
                $u = User::factory()->create([
                    'name' => $employee->nama,
                    'email' => $employee->email,
                    'password' => Hash::make('password'),
                    'employee_id' => $employee->id,
                    'karyawan_id' => $employee->id,
                    'location_id' => $loc->id,
                ]);
                $u->assignRole('Karyawan');
            }
        }

        // Create 3 Super Admin users
        for ($i = 1; $i <= 3; $i++) {
            $user = User::factory()->create([
                'name' => 'Super Admin ' . $i,
                'email' => 'superadmin' . $i . '@example.com',
                'password' => Hash::make('password'),
            ]);
            $user->assignRole('Super Admin');
        }

        // Create 2 Admin Lokasi users with assigned locations
        $locations = Location::take(2)->pluck('id');
        foreach ($locations as $idx => $locId) {
            $al = User::factory()->create([
                'name' => 'Admin Lokasi ' . ($idx + 1),
                'email' => 'adminlokasi' . ($idx + 1) . '@example.com',
                'password' => Hash::make('password'),
                'location_id' => $locId,
            ]);
            $al->assignRole('Admin Lokasi');
        }

        // Create 5 Karyawan users
        for ($i = 1; $i <= 5; $i++) {
            $user = User::factory()->create([
                'name' => 'Employee ' . $i,
                'email' => 'employee' . $i . '@example.com',
                'password' => Hash::make('password'),
                'location_id' => Location::inRandomOrder()->value('id'),
            ]);
            $user->assignRole('Karyawan');
        }

        // Assign masters to employees
        $masters = User::role('Super Admin')->pluck('id');
        Employee::all()->each(function ($employee) use ($masters) {
            $employee->update(['master_id' => $masters->random()]);
        });

        // Backfill roles for any users missing roles
        $this->call(UserRoleBackfillSeeder::class);
    }
}
