<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Overtime;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class OvertimeEmployeeCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Location Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Employee', 'guard_name' => 'web']);
    }

    private function createEmployee(): User
    {
        $location = Location::create([
            'name' => 'HQ '.Str::random(4),
            'code' => 'HQ'.Str::upper(Str::random(4)),
        ]);

        $user = User::factory()->create([
            'location_id' => $location->id,
        ]);
        $user->assignRole('Employee');

        return $user;
    }

    private function createLocationAdmins(Location $location, int $count = 1)
    {
        return User::factory()->count($count)->create([
            'location_id' => $location->id,
        ])->each(function (User $user) {
            $user->assignRole('Location Admin');
        });
    }

    private function createSuperAdmins(int $count = 2)
    {
        return User::factory()->count($count)->create()->each(function (User $user) {
            $user->assignRole('Super Admin');
        });
    }

    public function test_employee_can_view_create_form(): void
    {
        $employee = $this->createEmployee();
        $this->createLocationAdmins(Location::find($employee->location_id), 2);

        $this->assertTrue($employee->hasRole('Employee'));

        $response = $this->actingAs($employee)->get(route('overtime.create'));

        $response->assertStatus(200);
        $response->assertViewIs('overtime.create');
    }

    public function test_employee_can_submit_overtime_request(): void
    {
        $employee = $this->createEmployee();
        $location = Location::find($employee->location_id);
        $locationAdmins = $this->createLocationAdmins($location, 2);

        $payload = [
            'date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '18:00',
            'end_time' => '20:00',
            'reason' => 'Need to finish monthly report',
        ];

        $response = $this->actingAs($employee)->post(route('overtime.store'), $payload);

        $response->assertRedirect(route('overtime.index'));
        $this->assertDatabaseHas('overtime_requests', [
            'user_id' => $employee->id,
            'reason' => 'Need to finish monthly report',
            'status' => 'pending',
        ]);

        $overtime = Overtime::first();
        $this->assertNotNull($overtime);
        $this->assertEqualsCanonicalizing($locationAdmins->pluck('id')->toArray(), $overtime->selected_masters);
        $this->assertDatabaseCount('overtime_approvals', 2);
        $this->assertDatabaseHas('overtime_approvals', [
            'overtime_request_id' => $overtime->id,
            'status' => 'pending',
        ]);
    }

    public function test_employee_without_location_admins_falls_back_to_super_admin(): void
    {
        $employee = $this->createEmployee();
        $superAdmins = $this->createSuperAdmins(2);

        $payload = [
            'date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '18:30',
            'end_time' => '21:00',
            'reason' => 'No location admin available',
        ];

        $response = $this->actingAs($employee)->post(route('overtime.store'), $payload);

        $response->assertRedirect(route('overtime.index'));

        $overtime = Overtime::latest()->first();
        $this->assertEqualsCanonicalizing($superAdmins->pluck('id')->toArray(), $overtime->selected_masters);
    }

    public function test_employee_can_update_pending_request(): void
    {
        $employee = $this->createEmployee();
        $location = Location::find($employee->location_id);
        $locationAdmins = $this->createLocationAdmins($location, 2);

        $overtime = Overtime::create([
            'user_id' => $employee->id,
            'date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '11:00:00',
            'end_time' => '13:00:00',
            'duration_hours' => 2,
            'reason' => 'Initial reason',
            'selected_masters' => $locationAdmins->pluck('id')->toArray(),
            'status' => 'pending',
        ]);

        foreach ($locationAdmins as $master) {
            $overtime->approvals()->create([
                'master_id' => $master->id,
                'status' => 'pending',
            ]);
        }

        $payload = [
            'date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '19:00',
            'end_time' => '21:30',
            'reason' => 'Updated reason',
        ];

        $response = $this->actingAs($employee)->put(route('overtime.update', $overtime), $payload);

        $response->assertRedirect(route('overtime.show', $overtime->id));
        $overtime->refresh();
        $this->assertSame('Updated reason', $overtime->reason);
        $this->assertSame('pending', $overtime->status);
        $this->assertSame($locationAdmins->pluck('id')->sort()->values()->toArray(), collect($overtime->selected_masters)->sort()->values()->toArray());
        $this->assertDatabaseCount('overtime_approvals', 2);
    }

    public function test_employee_cannot_update_non_pending_request(): void
    {
        $employee = $this->createEmployee();
        $location = Location::find($employee->location_id);
        $locationAdmins = $this->createLocationAdmins($location, 2);

        $overtime = Overtime::create([
            'user_id' => $employee->id,
            'date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '11:00:00',
            'end_time' => '13:00:00',
            'duration_hours' => 2,
            'reason' => 'Initial reason',
            'selected_masters' => $locationAdmins->pluck('id')->toArray(),
            'status' => 'approved',
        ]);

        $payload = [
            'date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '19:00',
            'end_time' => '21:30',
            'reason' => 'Updated reason',
        ];

        $response = $this->actingAs($employee)->put(route('overtime.update', $overtime), $payload);

        $response->assertRedirect(route('overtime.show', $overtime));
        $response->assertSessionHasErrors();
    }

    public function test_employee_can_cancel_pending_request(): void
    {
        $employee = $this->createEmployee();
        $location = Location::find($employee->location_id);
        $locationAdmins = $this->createLocationAdmins($location, 2);

        $overtime = Overtime::create([
            'user_id' => $employee->id,
            'date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '11:00:00',
            'end_time' => '13:00:00',
            'duration_hours' => 2,
            'reason' => 'Initial reason',
            'selected_masters' => $locationAdmins->pluck('id')->toArray(),
            'status' => 'pending',
        ]);

        foreach ($locationAdmins as $master) {
            $overtime->approvals()->create([
                'master_id' => $master->id,
                'status' => 'pending',
            ]);
        }

        $response = $this->actingAs($employee)->delete(route('overtime.destroy', $overtime));

        $response->assertRedirect(route('overtime.index'));
        $this->assertDatabaseMissing('overtime_requests', ['id' => $overtime->id]);
        $this->assertDatabaseCount('overtime_approvals', 0);
    }

    public function test_employee_cannot_manage_other_employee_request(): void
    {
        $employee = $this->createEmployee();
        $otherEmployee = $this->createEmployee();
        $otherLocation = Location::find($otherEmployee->location_id);
        $locationAdmins = $this->createLocationAdmins($otherLocation, 2);

        $overtime = Overtime::create([
            'user_id' => $otherEmployee->id,
            'date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '11:00:00',
            'end_time' => '13:00:00',
            'duration_hours' => 2,
            'reason' => 'Initial reason',
            'selected_masters' => $locationAdmins->pluck('id')->toArray(),
            'status' => 'pending',
        ]);

        $responseEdit = $this->actingAs($employee)->get(route('overtime.edit', $overtime));
        $responseEdit->assertStatus(403);

        $responseDelete = $this->actingAs($employee)->delete(route('overtime.destroy', $overtime));
        $responseDelete->assertStatus(403);
    }

    public function test_location_admin_can_approve_request(): void
    {
        $employee = $this->createEmployee();
        $location = Location::find($employee->location_id);
        $locationAdmin = $this->createLocationAdmins($location, 1)->first();

        $payload = [
            'date' => now()->addDay()->format('Y-m-d'),
            'start_time' => '17:00',
            'end_time' => '19:00',
            'reason' => 'Need approval from location admin',
        ];

        $this->actingAs($employee)->post(route('overtime.store'), $payload);

        $overtime = Overtime::first();

        $response = $this->actingAs($locationAdmin)->patch(route('overtime.approve', $overtime), [
            'status' => 'approved',
            'notes' => 'Approved on-site',
        ]);

        $response->assertRedirect(route('overtime.show', $overtime));

        $this->assertDatabaseHas('overtime_approvals', [
            'overtime_request_id' => $overtime->id,
            'master_id' => $locationAdmin->id,
            'status' => 'approved',
        ]);

        $overtime->refresh();
        $this->assertSame('approved', $overtime->status);
    }
}
