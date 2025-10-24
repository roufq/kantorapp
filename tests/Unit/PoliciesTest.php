<?php

namespace Tests\Unit;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PoliciesTest extends TestCase
{
    use RefreshDatabase;

    private function ensureRoles(): void
    {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Super Admin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin Lokasi']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Karyawan']);
    }

    public function test_employee_policy_admin_lokasi_same_location_can_manage(): void
    {
        $this->ensureRoles();

        $loc = Location::create(['name' => 'A', 'code' => 'LA', 'timezone' => 'Asia/Jakarta', 'is_active' => true]);
        $admin = User::factory()->create(['location_id' => $loc->id]);
        $admin->assignRole('Admin Lokasi');

        $emp = Employee::create([
            'nama' => 'E1','email' => 'e1@example.com','divisi_id' => 1,'location_id' => $loc->id,
        ]);

        $this->assertTrue($admin->can('view', $emp));
        $this->assertTrue($admin->can('update', $emp));
        $this->assertTrue($admin->can('delete', $emp));
    }

    public function test_employee_policy_karyawan_can_view_update_own_only(): void
    {
        $this->ensureRoles();
        $loc = Location::create(['name' => 'A', 'code' => 'LB', 'timezone' => 'Asia/Jakarta', 'is_active' => true]);

        $emp = Employee::create([
            'nama' => 'Self','email' => 'self@example.com','divisi_id' => 1,'location_id' => $loc->id,
        ]);
        $user = User::factory()->create(['location_id' => $loc->id, 'employee_id' => $emp->id]);
        $user->assignRole('Karyawan');

        $this->assertTrue($user->can('view', $emp));
        $this->assertTrue($user->can('update', $emp));

        $other = Employee::create([
            'nama' => 'Other','email' => 'other@example.com','divisi_id' => 1,'location_id' => $loc->id,
        ]);
        $this->assertFalse($user->can('view', $other));
        $this->assertFalse($user->can('update', $other));
    }

    public function test_attendance_policy_rules(): void
    {
        $this->ensureRoles();
        $loc = Location::create(['name' => 'A', 'code' => 'LC', 'timezone' => 'Asia/Jakarta', 'is_active' => true]);
        $admin = User::factory()->create(['location_id' => $loc->id]);
        $admin->assignRole('Admin Lokasi');

        $empUser = User::factory()->create(['location_id' => $loc->id]);
        $empUser->assignRole('Karyawan');

        $att = Attendance::create([
            'user_id' => $empUser->id,
            'location_id' => $loc->id,
            'check_in_time' => now(),
        ]);

        $this->assertTrue($admin->can('viewAny', Attendance::class));
        $this->assertTrue($admin->can('view', $att));
        $this->assertTrue($admin->can('update', $att));

        $this->assertTrue($empUser->can('viewAny', Attendance::class));
        $this->assertTrue($empUser->can('view', $att));
        $this->assertFalse($empUser->can('update', $att));
    }
}

