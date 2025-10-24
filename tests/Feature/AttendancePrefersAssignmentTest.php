<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Location;
use App\Models\Shift;
use App\Models\ShiftAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AttendancePrefersAssignmentTest extends TestCase
{
    use RefreshDatabase;

    private function ensureRoles(): void
    {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Super Admin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin Lokasi']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Karyawan']);
    }

    public function test_checkin_uses_today_assignment_shift(): void
    {
        $this->ensureRoles();

        $loc = Location::create([
            'name' => 'Test Loc','code' => 'TLOC','timezone' => 'Asia/Jakarta','latitude' => -6.2,'longitude' => 106.8,'radius' => 500,'is_active' => true,
        ]);
        $user = User::create([
            'name' => 'Emp','email' => 'emp@test.com','password' => Hash::make('password'),'location_id' => $loc->id,
        ]);
        $user->assignRole('Karyawan');

        $shift = Shift::create([
            'name' => 'Full Day','code' => 'FULL','shift_type' => 'single','time_slots' => ['start' => '00:00','end' => '23:59'],'is_active' => true,
        ]);
        $shift->locations()->attach($loc->id);

        ShiftAssignment::create([
            'user_id' => $user->id,
            'shift_id' => $shift->id,
            'date' => now('Asia/Jakarta')->toDateString(),
            'status' => 'scheduled',
        ]);

        $this->actingAs($user);
        $resp = $this->post(route('attendance.checkin.post'), [
            'latitude' => -6.2,
            'longitude' => 106.8,
        ]);
        $resp->assertRedirect();

        $att = Attendance::where('user_id', $user->id)->latest('id')->first();
        $this->assertNotNull($att);
        $this->assertEquals($shift->id, $att->shift_id, 'attendance should use assigned shift id');
    }
}

