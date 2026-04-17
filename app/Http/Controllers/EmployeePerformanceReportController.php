<?php

namespace App\Http\Controllers;

use App\Exports\KpiExport;
use App\Models\Attendance;
use App\Models\Location;
use App\Models\Overtime;
use App\Models\Task;
use App\Models\TaskSlot;
use App\Models\EmployeeJobdeskAssignment;
use App\Models\JobdeskOutputTarget;
use App\Models\Jobdesk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class EmployeePerformanceReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $days = $this->normalizeDays((int) $request->get('days', 30));
        $start = now()->subDays($days - 1)->startOfDay();
        $end = now()->endOfDay();

        $search = $request->get('search');
        $employeeId = $request->get('employee_id');
        $locationId = $request->get('location_id');

        $usersQuery = User::role('Employee')->with('location');

        if ($user->hasRole('Location Admin')) {
            $locationId = $user->location_id;
            $usersQuery->where('location_id', $locationId);
        } elseif ($user->hasRole('Employee')) {
            $employeeId = $user->id;
            $usersQuery->where('id', $user->id);
        } elseif ($locationId) {
            $usersQuery->where('location_id', $locationId);
        }

        if ($employeeId) {
            $usersQuery->where('id', $employeeId);
        }

        if ($search) {
            $usersQuery->where('name', 'like', '%' . $search . '%');
        }

        $users = $usersQuery->orderBy('name')->paginate(15)->withQueryString();
        $metricsByUser = $this->buildMetrics($users, $start, $end, $days);

        $locations = ($user->hasRole('Super Admin') || $user->hasRole('HR'))
            ? Location::orderBy('name')->get()
            : collect();

        $employeeOptions = User::role('Employee')
            ->when($user->hasRole('Location Admin'), function ($query) use ($user) {
                $query->where('location_id', $user->location_id);
            })
            ->when($locationId && ($user->hasRole('Super Admin') || $user->hasRole('HR')), function ($query) use ($locationId) {
                $query->where('location_id', $locationId);
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('reports.employee-performance', compact(
            'users',
            'metricsByUser',
            'days',
            'search',
            'locationId',
            'employeeId',
            'locations',
            'employeeOptions'
        ));
    }

    public function export(Request $request)
    {
        $user = Auth::user();

        $days = $this->normalizeDays((int) $request->get('days', 30));
        $start = now()->subDays($days - 1)->startOfDay();
        $end = now()->endOfDay();

        $search = $request->get('search');
        $employeeId = $request->get('employee_id');
        $locationId = $request->get('location_id');

        $usersQuery = User::role('Employee')->with('location');

        if ($user->hasRole('Location Admin')) {
            $locationId = $user->location_id;
            $usersQuery->where('location_id', $locationId);
        } elseif ($user->hasRole('Employee')) {
            $employeeId = $user->id;
            $usersQuery->where('id', $user->id);
        } elseif ($locationId) {
            $usersQuery->where('location_id', $locationId);
        }

        if ($employeeId) {
            $usersQuery->where('id', $employeeId);
        }

        if ($search) {
            $usersQuery->where('name', 'like', '%' . $search . '%');
        }

        $users = $usersQuery->orderBy('name')->get();
        $metricsByUser = $this->buildMetrics($users, $start, $end, $days);

        $rows = [];
        foreach ($users as $emp) {
            $metrics = $metricsByUser[$emp->id] ?? [
                'attendance_rate' => 0,
                'attendance_days' => 0,
                'attendance_count' => 0,
                'late_count' => 0,
                'lateness_rate' => 0,
                'overtime_hours' => 0,
                'tasks_created' => 0,
                'tasks_completed' => 0,
                'task_productivity_rate' => 0,
                'output_points' => 0,
                'target_points' => 0,
                'output_efficiency_rate' => 0,
                'attendance_minutes' => 0,
                'min_attendance_minutes' => 0,
                'min_attendance_met' => null,
            ];
            $rows[] = [
                $emp->name,
                optional($emp->location)->name ?? '-',
                number_format($metrics['attendance_rate'], 2) . '%',
                $metrics['attendance_days'],
                number_format($metrics['lateness_rate'], 2) . '%',
                $metrics['late_count'],
                $metrics['attendance_count'],
                number_format($metrics['overtime_hours'], 2),
                $metrics['attendance_minutes'],
                $metrics['min_attendance_minutes'],
                is_null($metrics['min_attendance_met']) ? '-' : ($metrics['min_attendance_met'] ? __('Yes') : __('No')),
                number_format($metrics['output_efficiency_rate'], 2) . '%',
                number_format((float) $metrics['output_points'], 2),
                $metrics['target_points'],
                $metrics['tasks_completed'],
                $metrics['tasks_created'],
                number_format($metrics['task_productivity_rate'], 2) . '%',
            ];
        }

        $headings = [
            __('Name'),
            __('Location'),
            __('Attendance (%)'),
            __('Present (days)'),
            __('Lateness (%)'),
            __('Late (x)'),
            __('Total Attendance'),
            __('Overtime (hours)'),
            __('Present (minutes)'),
            __('Min Present (minutes)'),
            __('Min Present Met'),
            __('Output Efficiency (%)'),
            __('Output (points)'),
            __('Target (points)'),
            __('Tasks Completed'),
            __('Tasks Created'),
            __('Productivity (%)'),
        ];

        $filename = 'employee_performance_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new KpiExport($rows, $headings), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    private function normalizeDays(int $days): int
    {
        if ($days < 7) {
            return 7;
        }
        if ($days > 365) {
            return 365;
        }
        return $days;
    }

    private function buildMetrics($users, $start, $end, int $days): array
    {
        $userIds = collect($users)->pluck('id')->all();
        $attendanceAgg = collect();
        $overtimeAgg = collect();
        $tasksCreatedAgg = collect();
        $tasksCompletedAgg = collect();
        $outputAgg = collect();
        $attendanceMinutesAgg = collect();
        $targetPointsByEmployeeId = [];
        $minAttendanceByEmployee = [];

        if (!empty($userIds)) {
            $attendanceAgg = Attendance::select(
                'user_id',
                DB::raw('count(*) as attendance_count'),
                DB::raw('sum(case when is_late = 1 then 1 else 0 end) as late_count'),
                DB::raw('count(distinct date(check_in_time)) as attendance_days')
            )
                ->whereBetween('check_in_time', [$start, $end])
                ->whereIn('user_id', $userIds)
                ->groupBy('user_id')
                ->get()
                ->keyBy('user_id');

            $overtimeAgg = Overtime::select('user_id', DB::raw('sum(duration_hours) as overtime_hours'))
                ->where('status', 'approved')
                ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->whereIn('user_id', $userIds)
                ->groupBy('user_id')
                ->get()
                ->keyBy('user_id');

            $tasksCreatedAgg = Task::select('assigned_to as user_id', DB::raw('count(*) as tasks_created'))
                ->whereBetween('created_at', [$start, $end])
                ->whereIn('assigned_to', $userIds)
                ->groupBy('assigned_to')
                ->get()
                ->keyBy('user_id');

            $tasksCompletedAgg = Task::select('assigned_to as user_id', DB::raw('count(*) as tasks_completed'))
                ->where('status', 'completed')
                ->whereBetween('updated_at', [$start, $end])
                ->whereIn('assigned_to', $userIds)
                ->groupBy('assigned_to')
                ->get()
                ->keyBy('user_id');

            $attendanceMinutesAgg = Attendance::select(
                'user_id',
                DB::raw('sum(case when check_out_time is not null then TIMESTAMPDIFF(MINUTE, check_in_time, check_out_time) else 0 end) as attendance_minutes')
            )
                ->whereBetween('check_in_time', [$start, $end])
                ->whereIn('user_id', $userIds)
                ->groupBy('user_id')
                ->get()
                ->keyBy('user_id');

            $outputAgg = TaskSlot::select(
                'tasks.assigned_to as user_id',
                DB::raw('sum(task_catalogs.value * task_slots.percentage / 100) as output_points')
            )
                ->join('employee_tasks as tasks', 'tasks.id', '=', 'task_slots.task_id')
                ->join('task_catalogs', 'task_catalogs.id', '=', 'tasks.task_catalog_id')
                ->where('task_slots.status', 'approved')
                ->whereBetween('task_slots.approved_at', [$start, $end])
                ->whereIn('tasks.assigned_to', $userIds)
                ->whereIn('task_catalogs.unit', ['points', 'weight'])
                ->groupBy('tasks.assigned_to')
                ->get()
                ->keyBy('user_id');

            $userEmployeeMap = User::whereIn('id', $userIds)
                ->get(['id', 'employee_id', 'employee_id'])
                ->mapWithKeys(function ($u) {
                    return [$u->id => ($u->employee_id ?: $u->employee_id)];
                });
            $employeeIds = $userEmployeeMap->values()->filter()->unique()->values()->all();

            $assignments = EmployeeJobdeskAssignment::whereIn('employee_id', $employeeIds)
                ->where(function ($q) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', now()->toDateString());
                })
                ->get(['employee_id', 'jobdesk_id']);

            $jobdeskIds = $assignments->pluck('jobdesk_id')->unique()->values()->all();
            if (!empty($jobdeskIds)) {
                $year = (int) $start->year;
                $month = (int) $start->month;
                $targets = JobdeskOutputTarget::where('year', $year)
                    ->where('month', $month)
                    ->whereIn('jobdesk_id', $jobdeskIds)
                    ->whereIn('unit', ['points', 'weight'])
                    ->get();

                $targetByEmployee = [];
                foreach ($targets as $target) {
                    if ($target->employee_id) {
                        $targetByEmployee[$target->employee_id][$target->jobdesk_id] = (int) $target->target_value;
                    } else {
                        $targetByEmployee['default'][$target->jobdesk_id] = (int) $target->target_value;
                    }
                }

                $jobdeskMinMap = Jobdesk::whereIn('id', $jobdeskIds)
                    ->pluck('min_attendance_minutes', 'id');

                foreach ($employeeIds as $employeeId) {
                    $jobdeskList = $assignments->where('employee_id', $employeeId)->pluck('jobdesk_id')->unique();
                    $targetPoints = 0;
                    $minAttendance = 0;
                    foreach ($jobdeskList as $jobdeskId) {
                        if (isset($targetByEmployee[$employeeId][$jobdeskId])) {
                            $targetPoints += $targetByEmployee[$employeeId][$jobdeskId];
                        } elseif (isset($targetByEmployee['default'][$jobdeskId])) {
                            $targetPoints += $targetByEmployee['default'][$jobdeskId];
                        }
                        $minVal = (int) ($jobdeskMinMap[$jobdeskId] ?? 0);
                        if ($minVal > $minAttendance) {
                            $minAttendance = $minVal;
                        }
                    }
                    $targetPointsByEmployeeId[$employeeId] = $targetPoints;
                    $minAttendanceByEmployee[$employeeId] = $minAttendance;
                }
            }
        }

        $metricsByUser = [];
        foreach ($users as $row) {
            $attendance = $attendanceAgg->get($row->id);
            $overtime = $overtimeAgg->get($row->id);
            $tasksCreated = $tasksCreatedAgg->get($row->id);
            $tasksCompleted = $tasksCompletedAgg->get($row->id);
            $output = $outputAgg->get($row->id);
            $attendanceMinutesRow = $attendanceMinutesAgg->get($row->id);

            $attendanceCount = (int) ($attendance->attendance_count ?? 0);
            $attendanceDays = (int) ($attendance->attendance_days ?? 0);
            $lateCount = (int) ($attendance->late_count ?? 0);
            $latenessRate = $attendanceCount > 0 ? round(($lateCount / $attendanceCount) * 100, 2) : 0.0;
            $attendanceRate = $days > 0 ? round(min(100, ($attendanceDays / $days) * 100), 2) : 0.0;
            $overtimeHours = (float) ($overtime->overtime_hours ?? 0);

            $createdCount = (int) ($tasksCreated->tasks_created ?? 0);
            $completedCount = (int) ($tasksCompleted->tasks_completed ?? 0);
            $productivityRate = $createdCount > 0 ? round(($completedCount / $createdCount) * 100, 2) : 0.0;

            $employeeId = $row->employee_id ?? $row->employee_id;
            $outputPoints = (float) ($output->output_points ?? 0);
            $targetPoints = (int) ($targetPointsByEmployeeId[$employeeId] ?? 0);
            $efficiencyRate = $targetPoints > 0 ? round(($outputPoints / $targetPoints) * 100, 2) : 0.0;
            $attendanceMinutes = (int) ($attendanceMinutesRow->attendance_minutes ?? 0);
            $minAttendance = (int) ($minAttendanceByEmployee[$employeeId] ?? 0);
            $minAttendanceMet = $minAttendance > 0 ? $attendanceMinutes >= $minAttendance : null;

            $metricsByUser[$row->id] = [
                'attendance_rate' => $attendanceRate,
                'attendance_days' => $attendanceDays,
                'attendance_count' => $attendanceCount,
                'late_count' => $lateCount,
                'lateness_rate' => $latenessRate,
                'overtime_hours' => $overtimeHours,
                'tasks_created' => $createdCount,
                'tasks_completed' => $completedCount,
                'task_productivity_rate' => $productivityRate,
                'output_points' => $outputPoints,
                'target_points' => $targetPoints,
                'output_efficiency_rate' => $efficiencyRate,
                'attendance_minutes' => $attendanceMinutes,
                'min_attendance_minutes' => $minAttendance,
                'min_attendance_met' => $minAttendanceMet,
            ];
        }

        return $metricsByUser;
    }
}
