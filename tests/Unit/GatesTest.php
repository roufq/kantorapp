<?php

namespace Tests\Unit;

use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class GatesTest extends TestCase
{
    use RefreshDatabase;

    private function ensureRoles(): void
    {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Super Admin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Location Admin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Employee']);
    }

    public function test_manage_user_gate_admin_same_location(): void
    {
        $this->ensureRoles();
        $loc = Location::create(['name' => 'A', 'code' => 'LD', 'timezone' => 'Asia/Jakarta', 'is_active' => true]);
        $admin = User::factory()->create(['location_id' => $loc->id]);
        $admin->assignRole('Location Admin');
        $target = User::factory()->create(['location_id' => $loc->id]);

        $this->be($admin);
        $this->assertTrue(Gate::allows('manage-user', $target));
    }

    public function test_create_user_gate_admin_only_own_location(): void
    {
        $this->ensureRoles();
        $loc1 = Location::create(['name' => 'A', 'code' => 'LE', 'timezone' => 'Asia/Jakarta', 'is_active' => true]);
        $loc2 = Location::create(['name' => 'B', 'code' => 'LF', 'timezone' => 'Asia/Jakarta', 'is_active' => true]);
        $admin = User::factory()->create(['location_id' => $loc1->id]);
        $admin->assignRole('Location Admin');

        $this->be($admin);
        $this->assertTrue(Gate::allows('create-user', $loc1->id));
        $this->assertFalse(Gate::allows('create-user', $loc2->id));
    }

    public function test_update_user_location_gate(): void
    {
        $this->ensureRoles();
        $loc = Location::create(['name' => 'A', 'code' => 'LG', 'timezone' => 'Asia/Jakarta', 'is_active' => true]);
        $admin = User::factory()->create(['location_id' => $loc->id]);
        $admin->assignRole('Location Admin');
        $target = User::factory()->create(['location_id' => $loc->id]);

        $this->be($admin);
        $this->assertTrue(Gate::allows('update-user-location', $target));
    }
}

