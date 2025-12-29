<?php

namespace App\Http\Controllers;

use App\Exports\KpiExport;
use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class KpiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $days = $this->normalizeDays((int) $request->get('days', 30));
        $section = (string) $request->get('section', 'lateness');

        $kpiStart = now()->subDays($days - 1)->startOfDay();
        $kpiEnd = now()->endOfDay();
        $daysInPeriod = max(1, $kpiStart->diffInDays($kpiEnd) + 1);

        $attendanceQuery = Attendance::with(['user.employee', 'shift', 'location'])
            ->whereBetween('check_in_time', [$kpiStart, $kpiEnd]);
        $overtimeQuery = Overtime::with('user')
            ->where('status', 'approved')
            ->whereBetween('date', [$kpiStart->toDateString(), $kpiEnd->toDateString()]);
        $taskBaseQuery = Task::with('assignee');

        if ($user->hasRole('Super Admin')) {
            $totalEmployees = User::role('Karyawan')->count();
        } elseif ($user->hasRole('Admin Lokasi')) {
            $locationId = $user->location_id;
            $attendanceQuery->whereHas('user', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            });
            $overtimeQuery->whereHas('user', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            });
            $taskBaseQuery->whereHas('assignee', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            });
            $totalEmployees = User::role('Karyawan')->where('location_id', $locationId)->count();
        } else {
            $attendanceQuery->where('user_id', $user->id);
            $overtimeQuery->where('user_id', $user->id);
            $taskBaseQuery->where('assigned_to', $user->id);
            $totalEmployees = 1;
        }

        $attendanceCount = (int) (clone $attendanceQuery)->count();
        $lateCount = (int) (clone $attendanceQuery)->where('is_late', true)->count();
        $latenessRate = $attendanceCount > 0 ? round(($lateCount / $attendanceCount) * 100, 2) : 0.0;
        $attendanceRate = $totalEmployees > 0
            ? round(min(100, ($attendanceCount / ($totalEmployees * $daysInPeriod)) * 100), 2)
            : 0.0;

        $overtimeHours = (float) (clone $overtimeQuery)->sum('duration_hours');

        $tasksCreatedQuery = (clone $taskBaseQuery)->whereBetween('created_at', [$kpiStart, $kpiEnd]);
        $tasksCompletedQuery = (clone $taskBaseQuery)
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$kpiStart, $kpiEnd]);

        $tasksCreated = (int) (clone $tasksCreatedQuery)->count();
        $tasksCompleted = (int) (clone $tasksCompletedQuery)->count();
        $taskProductivityRate = $tasksCreated > 0 ? round(($tasksCompleted / $tasksCreated) * 100, 2) : 0.0;

        $kpiMetrics = [
            'lateness_rate' => $latenessRate,
            'late_count' => $lateCount,
            'attendance_rate' => $attendanceRate,
            'attendance_count' => $attendanceCount,
            'overtime_hours' => $overtimeHours,
            'tasks_created' => $tasksCreated,
            'tasks_completed' => $tasksCompleted,
            'task_productivity_rate' => $taskProductivityRate,
            'period_label' => $kpiStart->format('d M') . ' - ' . $kpiEnd->format('d M'),
        ];

        $lateAttendances = (clone $attendanceQuery)
            ->where('is_late', true)
            ->orderBy('check_in_time', 'desc')
            ->paginate(20, ['*'], 'lateness_page');
        $attendanceList = (clone $attendanceQuery)
            ->orderBy('check_in_time', 'desc')
            ->paginate(20, ['*'], 'attendance_page');
        $overtimeList = (clone $overtimeQuery)
            ->orderBy('date', 'desc')
            ->paginate(20, ['*'], 'overtime_page');
        $tasksCreatedList = (clone $tasksCreatedQuery)
            ->orderBy('created_at', 'desc')
            ->paginate(20, ['*'], 'tasks_created_page');
        $tasksCompletedList = (clone $tasksCompletedQuery)
            ->orderBy('updated_at', 'desc')
            ->paginate(20, ['*'], 'tasks_completed_page');

        return view('reports.kpi', compact(
            'days',
            'section',
            'kpiMetrics',
            'lateAttendances',
            'attendanceList',
            'overtimeList',
            'tasksCreatedList',
            'tasksCompletedList'
        ));
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $days = $this->normalizeDays((int) $request->get('days', 30));
        $section = (string) $request->get('section', 'lateness');

        $kpiStart = now()->subDays($days - 1)->startOfDay();
        $kpiEnd = now()->endOfDay();

        $attendanceQuery = Attendance::with(['user.employee', 'shift', 'location'])
            ->whereBetween('check_in_time', [$kpiStart, $kpiEnd]);
        $overtimeQuery = Overtime::with('user')
            ->where('status', 'approved')
            ->whereBetween('date', [$kpiStart->toDateString(), $kpiEnd->toDateString()]);
        $taskBaseQuery = Task::with('assignee');

        if ($user->hasRole('Super Admin')) {
            // no scope
        } elseif ($user->hasRole('Admin Lokasi')) {
            $locationId = $user->location_id;
            $attendanceQuery->whereHas('user', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            });
            $overtimeQuery->whereHas('user', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            });
            $taskBaseQuery->whereHas('assignee', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            });
        } else {
            $attendanceQuery->where('user_id', $user->id);
            $overtimeQuery->where('user_id', $user->id);
            $taskBaseQuery->where('assigned_to', $user->id);
        }

        $rows = [];
        $headings = [];
        $filename = 'kpi_export';

        if ($section === 'lateness' || $section === 'attendance') {
            $headings = ['Tanggal', 'Nama', 'Lokasi', 'Shift', 'Check In', 'Check Out', 'Terlambat'];
            $query = $section === 'lateness'
                ? (clone $attendanceQuery)->where('is_late', true)
                : clone $attendanceQuery;
            $rows = $query->orderBy('check_in_time', 'desc')->get()->map(function ($att) {
                return [
                    optional($att->check_in_time)->format('Y-m-d') ?? '-',
                    optional($att->user)->name ?? '-',
                    optional($att->location)->name ?? '-',
                    optional($att->shift)->name ?? '-',
                    optional($att->check_in_time)->format('H:i') ?? '-',
                    optional($att->check_out_time)->format('H:i') ?? '-',
                    $att->is_late ? 'Ya' : 'Tidak',
                ];
            })->all();
            $filename = $section === 'lateness' ? 'kpi_keterlambatan' : 'kpi_kehadiran';
        } elseif ($section === 'overtime') {
            $headings = ['Tanggal', 'Nama', 'Durasi (jam)', 'Alasan'];
            $rows = $overtimeQuery->orderBy('date', 'desc')->get()->map(function ($ot) {
                return [
                    optional($ot->date)->format('Y-m-d') ?? '-',
                    optional($ot->user)->name ?? '-',
                    number_format((float) $ot->duration_hours, 2),
                    $ot->reason ?? '-',
                ];
            })->all();
            $filename = 'kpi_overtime';
        } elseif ($section === 'tasks_created' || $section === 'tasks_completed') {
            if ($section === 'tasks_created') {
                $query = (clone $taskBaseQuery)->whereBetween('created_at', [$kpiStart, $kpiEnd]);
                $headings = ['Judul', 'Assignee', 'Status', 'Dibuat', 'Jatuh Tempo'];
                $rows = $query->orderBy('created_at', 'desc')->get()->map(function ($task) {
                    return [
                        $task->title,
                        optional($task->assignee)->name ?? '-',
                        $task->status,
                        optional($task->created_at)->format('Y-m-d') ?? '-',
                        optional($task->due_date)->format('Y-m-d') ?? '-',
                    ];
                })->all();
                $filename = 'kpi_tasks_created';
            } else {
                $query = (clone $taskBaseQuery)
                    ->where('status', 'completed')
                    ->whereBetween('updated_at', [$kpiStart, $kpiEnd]);
                $headings = ['Judul', 'Assignee', 'Status', 'Selesai', 'Dibuat'];
                $rows = $query->orderBy('updated_at', 'desc')->get()->map(function ($task) {
                    return [
                        $task->title,
                        optional($task->assignee)->name ?? '-',
                        $task->status,
                        optional($task->updated_at)->format('Y-m-d') ?? '-',
                        optional($task->created_at)->format('Y-m-d') ?? '-',
                    ];
                })->all();
                $filename = 'kpi_tasks_completed';
            }
        } else {
            return back()->with('error', 'Jenis export KPI tidak dikenali.');
        }

        [$writer, $extension] = $this->resolveExportWriter((string) $request->get('format', 'auto'));
        return Excel::download(new KpiExport($rows, $headings), $filename . '.' . $extension, $writer);
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

    private function resolveExportWriter(string $format): array
    {
        $format = strtolower($format);
        $supportsXls = defined('Maatwebsite\\Excel\\Excel::XLS');

        if ($format === 'csv') {
            return [\Maatwebsite\Excel\Excel::CSV, 'csv'];
        }
        if ($format === 'xls') {
            if ($supportsXls) {
                return [constant('Maatwebsite\\Excel\\Excel::XLS'), 'xls'];
            }
            return [\Maatwebsite\Excel\Excel::CSV, 'csv'];
        }
        if ($format === 'xlsx' || ($format === 'auto' && extension_loaded('zip'))) {
            return [\Maatwebsite\Excel\Excel::XLSX, 'xlsx'];
        }
        if ($supportsXls) {
            return [constant('Maatwebsite\\Excel\\Excel::XLS'), 'xls'];
        }
        return [\Maatwebsite\Excel\Excel::CSV, 'csv'];
    }
}
