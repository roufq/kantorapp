<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Division;
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

        // Create divisions
        Division::create(['nama' => 'HR']);
        Division::create(['nama' => 'IT']);
        Division::create(['nama' => 'Finance']);

        // Create employees
        Employee::create([
            'nama' => 'John Doe',
            'email' => 'john@example.com',
            'telepon' => '123456789',
            'alamat' => 'Address 1',
            'jabatan' => 'Manager',
            'departemen' => 'HR',
            'tanggal_lahir' => '1990-01-01',
            'divisi_id' => 1,
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
        ]);

        // Create 3 master users
        for ($i = 1; $i <= 3; $i++) {
            User::factory()->master()->create([
                'name' => 'Master ' . $i,
                'email' => 'master' . $i . '@example.com',
                'password' => Hash::make('password'),
            ]);
        }

        // Create 5 employee users
        for ($i = 1; $i <= 5; $i++) {
            User::factory()->employee()->create([
                'name' => 'Employee ' . $i,
                'email' => 'employee' . $i . '@example.com',
                'password' => Hash::make('password'),
            ]);
        }

        // Assign masters to employees
        $masters = User::where('role', 'master')->pluck('id');
        Employee::all()->each(function ($employee) use ($masters) {
            $employee->update(['master_id' => $masters->random()]);
        });
    }
}
