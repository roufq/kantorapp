<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Shift;
use App\Models\ShiftAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LocationAccessControlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles needed for tests (idempotent)
        Role::findOrCreate('Super Admin');
        Role::findOrCreate('Admin Lokasi');
    }

    public function test_admin_lokasi_cannot_edit_assignment_lokasi_lain(): void
    {
        $loc1 = Location::create(['name' => 'Loc 1', 'code' => 'L1', 'timezone' => 'Asia/Jakarta']);
        $loc2 = Location::create(['name' => 'Loc 2', 'code' => 'L2', 'timezone' => 'Asia/Jakarta']);

        $shift = Shift::create([
            'name' => 'Shift A',
            'code' => 'S1',
            'category' => 'office',
            'shift_type' => 'single',
            'time_slots' => ['start' => '09:00', 'end' => '17:00'],
            'is_active' => true,
        ]);
        $loc1->shifts()->attach($shift->id, ['category' => 'office', 'time_slots' => $shift->time_slots, 'is_default' => true]);
        $loc2->shifts()->attach($shift->id, ['category' => 'office', 'time_slots' => $shift->time_slots, 'is_default' => true]);

        $adminLoc1 = User::factory()->create(['location_id' => $loc1->id]);
        $adminLoc1->assignRole('Admin Lokasi');

        $userLoc2 = User::factory()->create(['location_id' => $loc2->id]);
        $pivotLoc2 = $loc2->shifts()->where('shifts.id', $shift->id)->first()->pivot;
        $assignmentLoc2 = ShiftAssignment::create([
            'user_id' => $userLoc2->id,
            'location_id' => $loc2->id,
            'shift_id' => $shift->id,
            'location_shift_id' => $pivotLoc2->id,
            'date' => now()->toDateString(),
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($adminLoc1)
            ->put(route('shift-assignments.update', $assignmentLoc2), [
                'user_id' => $userLoc2->id,
                'location_shift_id' => $pivotLoc2->id,
                'date' => now()->toDateString(),
                'status' => 'cancelled',
            ]);

        $this->assertTrue($response->isForbidden() || $response->isRedirection());
        $assignmentLoc2->refresh();
        $this->assertEquals('scheduled', $assignmentLoc2->status);
    }

    public function test_admin_lokasi_hanya_melihat_assignment_lokasinya_di_index(): void
    {
        $loc1 = Location::create(['name' => 'Loc 1', 'code' => 'L1', 'timezone' => 'Asia/Jakarta']);
        $loc2 = Location::create(['name' => 'Loc 2', 'code' => 'L2', 'timezone' => 'Asia/Jakarta']);

        $shift = Shift::create([
            'name' => 'Shift A',
            'code' => 'S1',
            'category' => 'office',
            'shift_type' => 'single',
            'time_slots' => ['start' => '09:00', 'end' => '17:00'],
            'is_active' => true,
        ]);
        $loc1->shifts()->attach($shift->id, ['category' => 'office', 'time_slots' => $shift->time_slots, 'is_default' => true]);
        $loc2->shifts()->attach($shift->id, ['category' => 'office', 'time_slots' => $shift->time_slots, 'is_default' => true]);

        $adminLoc1 = User::factory()->create(['location_id' => $loc1->id]);
        $adminLoc1->assignRole('Admin Lokasi');

        $userLoc1 = User::factory()->create(['location_id' => $loc1->id]);
        $userLoc2 = User::factory()->create(['location_id' => $loc2->id]);

        ShiftAssignment::create([
            'user_id' => $userLoc1->id,
            'location_id' => $loc1->id,
            'shift_id' => $shift->id,
            'location_shift_id' => $loc1->shifts()->where('shifts.id', $shift->id)->first()->pivot->id,
            'date' => now()->toDateString(),
            'status' => 'scheduled',
        ]);

        ShiftAssignment::create([
            'user_id' => $userLoc2->id,
            'location_id' => $loc2->id,
            'shift_id' => $shift->id,
            'location_shift_id' => $loc2->shifts()->where('shifts.id', $shift->id)->first()->pivot->id,
            'date' => now()->toDateString(),
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($adminLoc1)
            ->get(route('shift-assignments.index'));

        $response->assertStatus(200);
        $content = $response->getContent();
        $this->assertStringContainsString('Loc 1', $content);
        $this->assertStringContainsString($userLoc1->name, $content);
        $this->assertStringNotContainsString($userLoc2->name, $content);
    }
}
