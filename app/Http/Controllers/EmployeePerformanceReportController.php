<?php

namespace App\Http\Controllers;

use App\Exports\KpiExport;
use App\Models\Attendance;
use App\Models\Location;
use App\Models\Overtime;
use App\Models\Task;
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

        $usersQuery = User::role('Karyawan')->with('location');

        if ($user->hasRole('Admin Lokasi')) {
            $locationId = $user->location_id;
            $usersQuery->where('location_id', $locationId);
        } elseif ($user->hasRole('Karyawan')) {
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

        $locations = $user->hasRole('Super Admin')
            ? Location::orderBy('name')->get()
            : collect();

        $employeeOptions = User::role('Karyawan')
            ->when($user->hasRole('Admin Lokasi'), function ($query) use ($user) {
                $query->where('location_id', $user->location_id);
            })
            ->when($locationId && $user->hasRole('Super Admin'), function ($query) use ($locationId) {
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

        $usersQuery = User::role('Karyawan')->with('location');

        if ($user->hasRole('Admin Lokasi')) {
            $locationId = $user->location_id;
            $usersQuery->where('location_id', $locationId);
        } elseif ($user->hasRole('Karyawan')) {
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
                $metrics['tasks_completed'],
                $metrics['tasks_created'],
                number_format($metrics['task_productivity_rate'], 2) . '%',
            ];
        }

        $headings = [
            'Nama',
            'Lokasi',
            'Kehadiran (%)',
            'Hadir (hari)',
            'Keterlambatan (%)',
            'Terlambat (x)',
            'Total Kehadiran',
            'Overtime (jam)',
            'Tugas Selesai',
            'Tugas Dibuat',
            'Produktivitas (%)',
        ];

        $filename = 'performa_karyawan_' . now()->format('Ymd_His') . '.xlsx';
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
        }

        $metricsByUser = [];
        foreach ($users as $row) {
            $attendance = $attendanceAgg->get($row->id);
            $overtime = $overtimeAgg->get($row->id);
            $tasksCreated = $tasksCreatedAgg->get($row->id);
            $tasksCompleted = $tasksCompletedAgg->get($row->id);

            $attendanceCount = (int) ($attendance->attendance_count ?? 0);
            $attendanceDays = (int) ($attendance->attendance_days ?? 0);
            $lateCount = (int) ($attendance->late_count ?? 0);
            $latenessRate = $attendanceCount > 0 ? round(($lateCount / $attendanceCount) * 100, 2) : 0.0;
            $attendanceRate = $days > 0 ? round(min(100, ($attendanceDays / $days) * 100), 2) : 0.0;
            $overtimeHours = (float) ($overtime->overtime_hours ?? 0);

            $createdCount = (int) ($tasksCreated->tasks_created ?? 0);
            $completedCount = (int) ($tasksCompleted->tasks_completed ?? 0);
            $productivityRate = $createdCount > 0 ? round(($completedCount / $createdCount) * 100, 2) : 0.0;

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
            ];
        }

        return $metricsByUser;
    }
}
