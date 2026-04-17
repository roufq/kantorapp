<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Employee;
use App\Models\Location;
use App\Models\Division;
use App\Models\Jobdesk;
use App\Models\TaskCatalog;
use App\Models\EmployeeJobdeskAssignment;
use App\Models\JobdeskOutputTarget;
use App\Models\ApprovalRule;
use App\Models\EmployeePositionHistory;
use App\Models\EmployeeTransfer;
use App\Models\EmployeeContract;
use App\Models\Task;
use App\Models\TaskSlot;
use App\Models\TaskSlotHistory;
use App\Models\LocationWorkTarget;
use App\Models\EmployeeWorkRecap;
use Spatie\Permission\Models\Role;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $month = (int) $now->format('m');
        $year = (int) $now->format('Y');

        // Ensure roles exist when seeding this class directly
        $guard = config('auth.defaults.guard', 'web');
        Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => $guard]);
        Role::firstOrCreate(['name' => 'Location Admin', 'guard_name' => $guard]);
        Role::firstOrCreate(['name' => 'HR', 'guard_name' => $guard]);
        Role::firstOrCreate(['name' => 'Employee', 'guard_name' => $guard]);

        $locations = Location::all();
        $employees = Employee::all();
        $users = User::all();

        if ($locations->isEmpty() || $employees->isEmpty() || $users->isEmpty()) {
            return;
        }

        $superAdmin = User::role('Super Admin')->first() ?? $users->first();
        $hrUser = User::role('HR')->first();

        // Jobdesks (global + per-location)
        $globalJobdesk = Jobdesk::firstOrCreate(
            ['name' => 'General Office', 'location_id' => null],
            [
                'description' => 'Jobdesk umum untuk karyawan non-shift.',
                'role_scope' => 'Employee',
                'created_by' => $hrUser?->id ?? $superAdmin?->id,
                'is_active' => true,
                'min_attendance_minutes' => 360,
            ]
        );

        $locationJobdesks = [];
        foreach ($locations as $loc) {
            $name = 'Operasional - ' . ($loc->code ?? $loc->name ?? ('Lokasi ' . $loc->id));
            $locationJobdesks[$loc->id] = Jobdesk::firstOrCreate(
                ['name' => $name, 'location_id' => $loc->id],
                [
                    'description' => 'Jobdesk operasional per lokasi.',
                    'role_scope' => 'Employee',
                    'created_by' => $hrUser?->id ?? $superAdmin?->id,
                    'is_active' => true,
                    'min_attendance_minutes' => 420,
                ]
            );
        }

        // Task Catalogs
        $jobdesks = Jobdesk::all();
        foreach ($jobdesks as $jobdesk) {
            TaskCatalog::firstOrCreate(
                ['jobdesk_id' => $jobdesk->id, 'name' => 'Administrasi Harian'],
                [
                    'description' => 'Input & rekap harian.',
                    'unit' => 'points',
                    'value' => 5,
                    'task_type' => 'routine',
                    'is_active' => true,
                ]
            );
            TaskCatalog::firstOrCreate(
                ['jobdesk_id' => $jobdesk->id, 'name' => 'Audit Data'],
                [
                    'description' => 'Cek data dan verifikasi.',
                    'unit' => 'points',
                    'value' => 8,
                    'task_type' => 'project',
                    'is_active' => true,
                ]
            );
            TaskCatalog::firstOrCreate(
                ['jobdesk_id' => $jobdesk->id, 'name' => 'Layanan Pelanggan'],
                [
                    'description' => 'Menangani tiket/permintaan.',
                    'unit' => 'minutes',
                    'value' => 120,
                    'task_type' => 'routine',
                    'is_active' => true,
                ]
            );
        }

        // Assign jobdesk to employees (primary)
        foreach ($employees as $emp) {
            $jobdesk = $locationJobdesks[$emp->location_id] ?? $globalJobdesk;
            EmployeeJobdeskAssignment::firstOrCreate(
                ['employee_id' => $emp->id, 'jobdesk_id' => $jobdesk->id, 'end_date' => null],
                [
                    'is_primary' => true,
                    'start_date' => $now->copy()->subMonths(2)->toDateString(),
                    'created_by' => $hrUser?->id ?? $superAdmin?->id,
                ]
            );
        }

        // Output Targets (per jobdesk)
        foreach ($jobdesks as $jobdesk) {
            JobdeskOutputTarget::firstOrCreate(
                [
                    'jobdesk_id' => $jobdesk->id,
                    'employee_id' => null,
                    'year' => $year,
                    'month' => $month,
                ],
                [
                    'unit' => 'points',
                    'target_value' => 60,
                ]
            );
        }

        // Approval Rules
        ApprovalRule::firstOrCreate(
            ['scope' => 'task', 'department' => null, 'min_value' => 0],
            ['approval_level' => 'location_admin', 'is_active' => true]
        );
        ApprovalRule::firstOrCreate(
            ['scope' => 'task', 'department' => 'IT', 'min_value' => 50],
            ['approval_level' => 'super_admin', 'is_active' => true]
        );

        // Position history, transfers, contracts
        foreach ($employees as $emp) {
            EmployeePositionHistory::firstOrCreate(
                [
                    'employee_id' => $emp->id,
                    'title' => $emp->jabatan ?? 'Staff',
                    'department' => $emp->departemen,
                ],
                [
                    'start_date' => $emp->tanggal_masuk_kerja ?? $now->copy()->subYear()->toDateString(),
                    'created_by' => $hrUser?->id ?? $superAdmin?->id,
                ]
            );

            EmployeeContract::firstOrCreate(
                [
                    'employee_id' => $emp->id,
                    'contract_type' => 'PKWT',
                    'status' => 'active',
                ],
                [
                    'start_date' => $emp->tanggal_masuk_kerja ?? $now->copy()->subMonths(6)->toDateString(),
                    'end_date' => $now->copy()->addMonths(6)->toDateString(),
                    'created_by' => $hrUser?->id ?? $superAdmin?->id,
                ]
            );
        }

        if ($locations->count() > 1) {
            $from = $locations->first();
            $to = $locations->last();
            $emp = $employees->first();
            EmployeeTransfer::firstOrCreate(
                [
                    'employee_id' => $emp->id,
                    'from_location_id' => $from->id,
                    'to_location_id' => $to->id,
                ],
                [
                    'effective_date' => $now->copy()->subMonths(1)->toDateString(),
                    'reason' => 'Rotasi lokasi (dummy).',
                    'created_by' => $hrUser?->id ?? $superAdmin?->id,
                ]
            );
        }

        // Location work target & work recap (minutes) for current month
        foreach ($locations as $loc) {
            LocationWorkTarget::firstOrCreate(
                [
                    'location_id' => $loc->id,
                    'employee_id' => null,
                    'year' => $year,
                    'month' => $month,
                ],
                [
                    'target_minutes' => 9600,
                ]
            );
        }

        foreach ($employees->take(5) as $emp) {
            EmployeeWorkRecap::firstOrCreate(
                [
                    'employee_id' => $emp->id,
                    'location_id' => $emp->location_id,
                    'year' => $year,
                    'month' => $month,
                ],
                [
                    'slot_minutes_approved' => 600,
                    'attendance_minutes' => 1200,
                    'total_minutes' => 1800,
                ]
            );
        }

        // Create a few tasks + slots based on catalog
        $catalogs = TaskCatalog::where('is_active', true)->limit(5)->get();
        $assignees = User::role('Employee')->take(5)->get();
        foreach ($assignees as $assignee) {
            $catalog = $catalogs->random();
            $title = $catalog->name . ' - ' . $assignee->name;
            $duration = $catalog->unit === 'minutes' ? $catalog->value : 120;

            $task = Task::firstOrCreate(
                [
                    'title' => $title,
                    'assigned_to' => $assignee->id,
                ],
                [
                    'description' => 'Dummy task dari catalog.',
                    'task_catalog_id' => $catalog->id,
                    'assigned_by' => $superAdmin?->id ?? $assignee->id,
                    'status' => 'completed',
                    'progress' => 100,
                    'due_date' => $now->copy()->addDays(7),
                    'duration_minutes' => $duration,
                    'requires_approval' => false,
                    'approval_status' => 'approved',
                    'approval_level' => 'none',
                    'approved_by' => $superAdmin?->id,
                    'approved_at' => $now,
                ]
            );

            if ($task->slots()->count() === 0) {
                $slot = TaskSlot::create([
                    'task_id' => $task->id,
                    'name' => 'Selesai',
                    'percentage' => 100,
                    'minutes' => $duration,
                    'order' => 0,
                    'created_by' => $superAdmin?->id ?? $assignee->id,
                    'status' => 'approved',
                    'approved_by' => $superAdmin?->id ?? $assignee->id,
                    'approved_at' => $now,
                ]);
                TaskSlotHistory::create([
                    'task_slot_id' => $slot->id,
                    'action' => 'approved',
                    'data_after' => $slot->toArray(),
                    'actor_id' => $superAdmin?->id ?? $assignee->id,
                ]);
            }
        }
    }
}
