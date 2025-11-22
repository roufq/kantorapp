<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserLocationAssignmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_lokasi_transfers_only_within_own_location()
    {
        $l1 = Location::create(['name' => 'L1', 'code' => 'L1']);
        $l2 = Location::create(['name' => 'L2', 'code' => 'L2']);

        $admin = User::factory()->create(['location_id' => $l1->id]);
        $admin->assignRole('Admin Lokasi');

        $emp = User::factory()->create(['location_id' => $l1->id]);
        $emp->assignRole('Karyawan');

        // Can view and update same location user
        $this->actingAs($admin)->get("/users/{$emp->id}")->assertStatus(200);

        // Attempt to transfer to another location should be unauthorized by gate
        $this->actingAs($admin)
            ->patch("/users/{$emp->id}", ['location_id' => $l2->id, 'name' => $emp->name, 'email' => $emp->email])
            ->assertStatus(403);
    }
}

