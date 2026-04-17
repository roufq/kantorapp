<?php

namespace Tests\Unit;

use App\Models\Location;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_super_admin_can_manage_any_user()
    {
        $super = User::factory()->create();
        $super->assignRole('Super Admin');

        $loc = Location::create(['name' => 'L1', 'code' => 'L1', 'timezone' => 'Asia/Jakarta', 'is_active' => true]);
        $u = User::factory()->create(['location_id' => $loc->id]);

        $this->assertTrue(Gate::forUser($super)->allows('manage-user', $u));
        $this->assertTrue(Gate::forUser($super)->allows('update-user-location', $u));
        $this->assertTrue(Gate::forUser($super)->allows('create-user', $loc->id));
    }

    public function test_location_admin_only_manages_same_location()
    {
        $l1 = Location::create(['name' => 'A', 'code' => 'A', 'timezone' => 'Asia/Jakarta', 'is_active' => true]);
        $l2 = Location::create(['name' => 'B', 'code' => 'B', 'timezone' => 'Asia/Jakarta', 'is_active' => true]);

        $admin = User::factory()->create(['location_id' => $l1->id]);
        $admin->assignRole('Location Admin');

        $u1 = User::factory()->create(['location_id' => $l1->id]);
        $u2 = User::factory()->create(['location_id' => $l2->id]);

        $this->assertTrue(Gate::forUser($admin)->allows('manage-user', $u1));
        $this->assertFalse(Gate::forUser($admin)->allows('manage-user', $u2));
        $this->assertTrue(Gate::forUser($admin)->allows('update-user-location', $u1));
        $this->assertFalse(Gate::forUser($admin)->allows('update-user-location', $u2));
        $this->assertTrue(Gate::forUser($admin)->allows('create-user', $l1->id));
        $this->assertFalse(Gate::forUser($admin)->allows('create-user', $l2->id));
    }
}

