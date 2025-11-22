<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Location;
use App\Models\Overtime;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    public function test_dashboard_metrics_are_computed_for_location_admin()
    {
        $l1 = Location::create(['name' => 'L1', 'code' => 'L1']);
        $l2 = Location::create(['name' => 'L2', 'code' => 'L2']);

        // Users
        $admin = User::factory()->create(['location_id' => $l1->id]);
        $admin->assignRole('Admin Lokasi');
        $u1 = User::factory()->create(['location_id' => $l1->id]);
        $u1->assignRole('Karyawan');
        $u2 = User::factory()->create(['location_id' => $l1->id]);
        $u2->assignRole('Karyawan');
        $u3 = User::factory()->create(['location_id' => $l2->id]);
        $u3->assignRole('Karyawan');

        // Tasks: for L1 -> 2 total, 1 completed; for L2 -> 1 completed
        Task::create(['title' => 't1', 'description' => 'd', 'assigned_by' => $admin->id, 'assigned_to' => $u1->id, 'status' => 'completed']);
        Task::create(['title' => 't2', 'description' => 'd', 'assigned_by' => $admin->id, 'assigned_to' => $u2->id, 'status' => 'pending']);
        Task::create(['title' => 't3', 'description' => 'd', 'assigned_by' => $admin->id, 'assigned_to' => $u3->id, 'status' => 'completed']);

        // Attendance today: only u1 checked in for L1; none for L2
        Attendance::create(['user_id' => $u1->id, 'check_in_time' => now(), 'approval_status' => 'approved']);

        // Overtime last 30d: 2 hours for u1 (L1), 1 hour for u3 (L2)
        Overtime::create([
            'user_id' => $u1->id,
            'date' => now()->toDateString(),
            'start_time' => now()->setTime(18,0),
            'end_time' => now()->setTime(20,0),
            'duration_hours' => 2,
            'reason' => 'load test',
            'status' => 'approved',
            'selected_masters' => []
        ]);
        Overtime::create([
            'user_id' => $u3->id,
            'date' => now()->toDateString(),
            'start_time' => now()->setTime(19,0),
            'end_time' => now()->setTime(20,0),
            'duration_hours' => 1,
            'reason' => 'load test',
            'status' => 'approved',
            'selected_masters' => []
        ]);

        $response = $this->actingAs($admin)->get('/dashboard')->assertStatus(200);
        $html = $response->getContent();

        // Task completion rate for L1: 1/2 = 50%
        $this->assertStringContainsString('50.00%', $html);
        // Attendance rate for L1: 1/2 = 50%
        $this->assertStringContainsString('Attendance Rate (Today)', $html);
        $this->assertStringContainsString('50.00%', $html);
        // Overtime hours L1: 2.00 hrs
        $this->assertStringContainsString('2.00 hrs', $html);
    }
}
