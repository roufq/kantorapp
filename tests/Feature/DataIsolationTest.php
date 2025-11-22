<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_admin_lokasi_sees_only_their_location_users_and_tasks()
    {
        $l1 = Location::create(['name' => 'L1', 'code' => 'L1']);
        $l2 = Location::create(['name' => 'L2', 'code' => 'L2']);

        $admin = User::factory()->create(['name' => 'Admin L1', 'location_id' => $l1->id]);
        $admin->assignRole('Admin Lokasi');

        $u1 = User::factory()->create(['name' => 'Alice L1', 'location_id' => $l1->id]);
        $u1->assignRole('Karyawan');
        $u2 = User::factory()->create(['name' => 'Bob L2', 'location_id' => $l2->id]);
        $u2->assignRole('Karyawan');

        Task::create(['title' => 'Task L1', 'description' => 'd', 'assigned_by' => $admin->id, 'assigned_to' => $u1->id, 'status' => 'pending']);
        Task::create(['title' => 'Task L2', 'description' => 'd', 'assigned_by' => $admin->id, 'assigned_to' => $u2->id, 'status' => 'pending']);

        // Users index should not include Bob L2
        $this->actingAs($admin)->get('/users')->assertStatus(200)
            ->assertSee('Alice L1')
            ->assertDontSee('Bob L2');

        // Tasks index should not include Task L2
        $this->actingAs($admin)->get('/tasks')->assertStatus(200)
            ->assertSee('Task L1')
            ->assertDontSee('Task L2');
    }
}

