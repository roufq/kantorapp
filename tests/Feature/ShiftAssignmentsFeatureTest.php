<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ShiftAssignmentsFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function ensureRoles(): void
    {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Super Admin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin Lokasi']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Karyawan']);
    }

    public function test_admin_lokasi_can_create_assignment_for_own_location(): void
    {
        $this->ensureRoles();

        $loc = Location::create([
            'name' => 'Loc A', 'code' => 'LOCA', 'timezone' => 'Asia/Jakarta', 'is_active' => true,
        ]);
        $admin = User::create([
            'name' => 'AL', 'email' => 'al@example.com', 'password' => Hash::make('password'), 'location_id' => $loc->id,
        ]);
        $admin->assignRole('Admin Lokasi');

        $emp = User::create([
            'name' => 'Emp', 'email' => 'emp@example.com', 'password' => Hash::make('password'), 'location_id' => $loc->id,
        ]);
        $emp->assignRole('Karyawan');

        $shift = Shift::create([
            'name' => 'Pagi', 'code' => 'PAGI', 'shift_type' => 'single', 'time_slots' => ['start' => '06:00', 'end' => '14:00'], 'is_active' => true,
        ]);
        $shift->locations()->attach($loc->id);

        $this->actingAs($admin);
        $resp = $this->post(route('shift-assignments.store'), [
            'user_id' => $emp->id,
            'shift_id' => $shift->id,
            'date' => now('Asia/Jakarta')->toDateString(),
            'status' => 'scheduled',
        ]);
        $resp->assertRedirect(route('shift-assignments.index'));
        $this->assertDatabaseHas('shift_assignments', [
            'user_id' => $emp->id,
            'shift_id' => $shift->id,
        ]);
    }

    public function test_admin_lokasi_cannot_create_assignment_for_other_location(): void
    {
        $this->ensureRoles();

        $locA = Location::create(['name' => 'A', 'code' => 'A1', 'timezone' => 'Asia/Jakarta', 'is_active' => true]);
        $locB = Location::create(['name' => 'B', 'code' => 'B1', 'timezone' => 'Asia/Jakarta', 'is_active' => true]);

        $adminA = User::create(['name' => 'AL A', 'email' => 'ala@example.com', 'password' => Hash::make('password'), 'location_id' => $locA->id]);
        $adminA->assignRole('Admin Lokasi');

        $empB = User::create(['name' => 'Emp B', 'email' => 'empb@example.com', 'password' => Hash::make('password'), 'location_id' => $locB->id]);
        $empB->assignRole('Karyawan');

        $shiftB = Shift::create(['name' => 'Siang', 'code' => 'SIANG', 'shift_type' => 'single', 'time_slots' => ['start' => '14:00', 'end' => '22:00'], 'is_active' => true]);
        $shiftB->locations()->attach($locB->id);

        $this->actingAs($adminA);
        $resp = $this->post(route('shift-assignments.store'), [
            'user_id' => $empB->id,
            'shift_id' => $shiftB->id,
            'date' => now('Asia/Jakarta')->toDateString(),
        ]);
        $resp->assertStatus(403);
    }
}

