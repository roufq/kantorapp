<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\Message;
use App\Models\Division;
use App\Models\Employee;
use App\Models\User;
use App\Models\Attendance;
use App\Models\ShiftAssignment;
use App\Models\Overtime;
use App\Models\Holiday;
use App\Models\EmployeeLeave;
use App\Services\WorkdayService;
use App\Models\EmployeeAbsence;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Fetch tasks based on roles with search
        if ($user->hasRole('Super Admin')) {
            // Super Admin sees all tasks
            $query = Task::with('assignee', 'assigner');
        } else if ($user->hasRole('Admin Lokasi')) {
            // Admin Lokasi sees tasks for users in their location
            $locationId = $user->location_id;
            $query = Task::whereHas('assignee', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            })->with('assignee', 'assigner');
        } else {
            // Karyawan sees only their own tasks
            $query = Task::where('assigned_to', $user->id)->with('assignee', 'assigner');
        }

        // Search by assignee name or task title
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhereHas('assignee', function ($subQ) use ($search) {
                      $subQ->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(10);

        $unreadMessages = Message::where('receiver_id', $user->id)->whereNull('read_at')->count();
        $today = now()->toDateString();
        $todayAssignment = null;
        $todayAssignmentsCount = 0;
        $recentAssignments = collect();
        $myUpcomingAssignments = collect();
        $attendanceList = collect();

        // Get counts for dashboard (location-aware for non Super Admin)
        if ($user->hasRole('Super Admin')) {
            $totalMasters = User::role('Super Admin')->count();
            $totalEmployees = User::role('Karyawan')->count();
            $totalTasks = Task::count();
            $totalMessages = Message::count(); // keep global for admins
            $totalUsers = User::count();
            $totalDivisions = Division::count();
            $totalKaryawans = Employee::count();
            $todayAssignmentsCount = ShiftAssignment::whereDate('date', $today)->count();
            $absencesTodayCount = EmployeeAbsence::whereDate('date', $today)->count();
        } else {
            $locationId = $user->location_id;
            $totalMasters = User::role('Super Admin')->count(); // global masters
            $totalEmployees = User::role('Karyawan')->where('location_id', $locationId)->count();
            $totalTasks = Task::whereHas('assignee', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            })->count();
            $totalMessages = Message::where(function ($q) use ($locationId) {
                $q->whereHas('receiver', function ($sq) use ($locationId) { $sq->where('location_id', $locationId); })
                  ->orWhereHas('sender', function ($sq) use ($locationId) { $sq->where('location_id', $locationId); });
            })->distinct('id')->count('id');
            $totalUsers = User::where('location_id', $locationId)->count();
            $totalDivisions = Division::count(); // divisions not location-specific yet
            $totalKaryawans = Employee::where('location_id', $locationId)->count();
            if ($user->hasRole('Admin Lokasi')) {
                $todayAssignmentsCount = ShiftAssignment::whereDate('date', $today)
                    ->whereHas('user', function ($q) use ($locationId) { $q->where('location_id', $locationId); })
                    ->count();
                $absencesTodayCount = EmployeeAbsence::whereDate('date', $today)->where('location_id', $locationId)->count();
                $recentAssignments = ShiftAssignment::with(['user','shift'])
                    ->whereHas('user', function ($q) use ($locationId) { $q->where('location_id', $locationId); })
                    ->whereDate('date', '>=', $today)
                    ->orderBy('date', 'asc')
                    ->limit(5)
                    ->get();
                $attendanceList = Attendance::with('user')
                    ->where(function ($q) use ($today) {
                        $q->whereDate('check_in_time', $today)->orWhereDate('check_out_time', $today);
                    })
                    ->whereHas('user', function ($q) use ($locationId) {
                        $q->where('location_id', $locationId);
                    })
                    ->orderBy('check_in_time', 'desc')
                    ->get();
            }
        }

        // Location performance metrics
        $locationMetrics = [
            'task_completion_rate' => 0.0,
            'attendance_rate_today' => 0.0,
            'overtime_hours_30d' => 0.0,
        ];

        if ($user->hasRole('Super Admin')) {
            $totalTasksAll = Task::count();
            $completedTasksAll = Task::where('status', 'completed')->count();
            $locationMetrics['task_completion_rate'] = $totalTasksAll > 0 ? round(($completedTasksAll / $totalTasksAll) * 100, 2) : 0.0;

            $totalEmployeesAll = User::role('Karyawan')->count();
            $checkedInTodayAll = Attendance::whereDate('check_in_time', $today)->distinct('user_id')->count('user_id');
            $locationMetrics['attendance_rate_today'] = $totalEmployeesAll > 0 ? round(($checkedInTodayAll / $totalEmployeesAll) * 100, 2) : 0.0;

            $locationMetrics['overtime_hours_30d'] = (float) Overtime::whereDate('date', '>=', now()->subDays(30)->toDateString())
                ->sum('duration_hours');
        } else {
            $locationId = $user->location_id;
            $totalTasksLoc = Task::whereHas('assignee', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            })->count();
            $completedTasksLoc = Task::where('status', 'completed')
                ->whereHas('assignee', function ($q) use ($locationId) {
                    $q->where('location_id', $locationId);
                })->count();
            $locationMetrics['task_completion_rate'] = $totalTasksLoc > 0 ? round(($completedTasksLoc / $totalTasksLoc) * 100, 2) : 0.0;

            $totalEmployeesLoc = User::role('Karyawan')->where('location_id', $locationId)->count();
            $checkedInTodayLoc = Attendance::whereDate('check_in_time', $today)
                ->whereHas('user', function ($q) use ($locationId) { $q->where('location_id', $locationId); })
                ->distinct('user_id')->count('user_id');
            $locationMetrics['attendance_rate_today'] = $totalEmployeesLoc > 0 ? round(($checkedInTodayLoc / $totalEmployeesLoc) * 100, 2) : 0.0;

            $locationMetrics['overtime_hours_30d'] = (float) Overtime::whereDate('date', '>=', now()->subDays(30)->toDateString())
                ->whereHas('user', function ($q) use ($locationId) { $q->where('location_id', $locationId); })
                ->sum('duration_hours');
        }

        if ($user->hasRole('Karyawan')) {
            $todayAssignment = ShiftAssignment::with('shift')
                ->where('user_id', $user->id)
                ->whereDate('date', $today)
                ->first();
            $myUpcomingAssignments = ShiftAssignment::with('shift')
                ->where('user_id', $user->id)
                ->whereDate('date', '>=', $today)
                ->orderBy('date', 'asc')
                ->limit(5)
                ->get();
            // Absence for today
            $absencesTodayCount = EmployeeAbsence::where('user_id', $user->id)->whereDate('date', $today)->count();
            $attendanceList = Attendance::with('user')
                ->where('user_id', $user->id)
                ->where(function ($q) use ($today) {
                    $q->whereDate('check_in_time', $today)->orWhereDate('check_out_time', $today);
                })
                ->orderBy('check_in_time', 'desc')
                ->get();
        }

        // Notices for holidays/leaves relevant to the user (cached)
        $cacheKeyToday = 'dash:notices:today:' . $user->id . ':' . $today;
        $todayNotices = Cache::remember($cacheKeyToday, now()->addMinutes(10), function () use ($user, $today) {
            $notices = [];
            if (!WorkdayService::isWorkingDay($user, now())) {
                $holidaysToday = Holiday::active()
                    ->whereDate('date', $today)
                    ->where(function ($q) use ($user) {
                        $q->where('is_national', true);
                        if ($user->location_id) {
                            $q->orWhere(function ($qq) use ($user) {
                                $qq->where('is_national', false)->where('location_id', $user->location_id);
                            });
                        }
                    })->get();
                foreach ($holidaysToday as $h) {
                    $notices[] = ($h->is_national ? 'Libur Nasional' : 'Libur Lokasi') . ': ' . $h->name;
                }
                if (!$holidaysToday->count() && WorkdayService::isWeeklyOff($user, now())) {
                    $notices[] = 'Weekly Off';
                }
                $onLeave = EmployeeLeave::where('user_id', $user->id)->where('status', 'approved')
                    ->whereDate('start_date', '<=', $today)->whereDate('end_date', '>=', $today)->first();
                if ($onLeave) {
                    $notices[] = 'Izin/Cuti (' . $onLeave->type . ')';
                }
            }
            return $notices;
        });

        $cacheKeyUpcoming = 'dash:notices:upcoming:' . $user->id . ':' . $today;
        $upcomingNotices = Cache::remember($cacheKeyUpcoming, now()->addMinutes(10), function () use ($user) {
            $list = [];
            for ($i = 1; $i <= 14 && count($list) < 5; $i++) {
                $d = now()->copy()->addDays($i);
                $dateStr = $d->toDateString();
                $labels = [];
                $holidays = Holiday::active()->whereDate('date', $dateStr)
                    ->where(function ($q) use ($user) {
                        $q->where('is_national', true);
                        if ($user->location_id) {
                            $q->orWhere(function ($qq) use ($user) {
                                $qq->where('is_national', false)->where('location_id', $user->location_id);
                            });
                        }
                    })->get();
                foreach ($holidays as $h) {
                    $labels[] = ($h->is_national ? 'Libur Nasional' : 'Libur Lokasi') . ': ' . $h->name;
                }
                if (WorkdayService::isWeeklyOff($user, $d)) {
                    $labels[] = 'Weekly Off';
                }
                $onLeave = EmployeeLeave::where('user_id', $user->id)->where('status', 'approved')
                    ->whereDate('start_date', '<=', $dateStr)->whereDate('end_date', '>=', $dateStr)->exists();
                if ($onLeave) {
                    $labels[] = 'Izin/Cuti';
                }
                if (!empty($labels)) {
                    $list[] = [
                        'date' => $d->format('Y-m-d'),
                        'labels' => array_values(array_unique($labels)),
                    ];
                }
            }
            return $list;
        });

        // Get today's attendance for the user (and for Admin Lokasi themselves)
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('check_in_time', now()->toDateString())
            ->first();

        return view('dashboard', compact(
            'user', 'tasks', 'unreadMessages',
            'totalMasters', 'totalEmployees', 'totalTasks', 'totalMessages', 'totalUsers', 'totalDivisions', 'totalKaryawans',
            'todayAttendance', 'todayAssignment', 'todayAssignmentsCount', 'recentAssignments', 'myUpcomingAssignments', 'locationMetrics', 'todayNotices', 'upcomingNotices', 'absencesTodayCount'
        ))->with('attendanceList', $attendanceList);
    }
}
