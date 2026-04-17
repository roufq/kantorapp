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
use App\Models\LocationShift;
use App\Models\Overtime;
use App\Models\Holiday;
use App\Models\EmployeeLeave;
use App\Services\WorkdayService;
use App\Models\EmployeeAbsence;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Location;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Fetch tasks based on roles with search
        if ($user->hasRole('Super Admin')) {
            // Super Admin sees all tasks
            $query = Task::with('assignee', 'assigner');
        } else if ($user->hasRole('Location Admin')) {
            // Location Admin sees tasks for users in their location
            $locationId = $user->location_id;
            $query = Task::whereHas('assignee', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            })->with('assignee', 'assigner');
        } else {
            // Employee sees only their own tasks
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
        $todayPlannedDate = null;
        $chartMetrics = [
            'total_employees' => 0,
            'total_tasks' => 0,
            'completed_tasks' => 0,
            'checked_in_today' => 0,
            'attendance_total' => 0,
            'total_masters' => 0,
            'total_users' => 0,
            'total_divisions' => 0,
            'total_messages' => 0,
            'unread_messages' => 0,
            'overtime_hours_30d' => 0,
        ];

        // Get counts for dashboard (location-aware for non Super Admin)
        if ($user->hasRole('Super Admin')) {
            $totalMasters = User::role('Super Admin')->count();
            $totalEmployees = User::role('Employee')->count();
            $totalTasks = Task::count();
            $totalMessages = Message::count(); // keep global for admins
            $totalUsers = User::count();
            $totalDivisions = Division::count();
            $totalEmployees = Employee::count();
            $todayAssignmentsCount = ShiftAssignment::whereDate('date', $today)->count();
            $absencesTodayCount = EmployeeAbsence::whereDate('date', $today)->count();

            $chartMetrics['total_employees'] = $totalEmployees;
            $chartMetrics['total_tasks'] = $totalTasks;
            $chartMetrics['completed_tasks'] = Task::where('status', 'completed')->count();
            $chartMetrics['checked_in_today'] = Attendance::whereDate('check_in_time', $today)->distinct('user_id')->count('user_id');
            $chartMetrics['attendance_total'] = $totalEmployees;
            $chartMetrics['total_masters'] = $totalMasters;
            $chartMetrics['total_users'] = $totalUsers;
            $chartMetrics['total_divisions'] = $totalDivisions;
            $chartMetrics['total_messages'] = $totalMessages;
            $chartMetrics['unread_messages'] = $unreadMessages;
        } else {
            $locationId = $user->location_id;
            $totalMasters = User::role('Super Admin')->count(); // global masters
            $totalEmployees = User::role('Employee')->where('location_id', $locationId)->count();
            $totalTasks = Task::whereHas('assignee', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            })->count();
            $totalMessages = Message::where(function ($q) use ($locationId) {
                $q->whereHas('receiver', function ($sq) use ($locationId) { $sq->where('location_id', $locationId); })
                  ->orWhereHas('sender', function ($sq) use ($locationId) { $sq->where('location_id', $locationId); });
            })->distinct('id')->count('id');
            $totalUsers = User::where('location_id', $locationId)->count();
            $totalDivisions = Division::count(); // divisions not location-specific yet
            $totalEmployees = Employee::where('location_id', $locationId)->count();

            $chartMetrics['total_employees'] = $totalEmployees;
            $chartMetrics['total_tasks'] = $totalTasks;
            $chartMetrics['completed_tasks'] = Task::where('status', 'completed')
                ->whereHas('assignee', function ($q) use ($locationId) {
                    $q->where('location_id', $locationId);
                })
                ->count();
            $chartMetrics['checked_in_today'] = Attendance::whereDate('check_in_time', $today)
                ->whereHas('user', function ($q) use ($locationId) {
                    $q->where('location_id', $locationId);
                })
                ->distinct('user_id')
                ->count('user_id');
            $chartMetrics['attendance_total'] = $totalEmployees;
            $chartMetrics['total_masters'] = $totalMasters;
            $chartMetrics['total_users'] = $totalUsers;
            $chartMetrics['total_divisions'] = $totalDivisions;
            $chartMetrics['total_messages'] = $totalMessages;
            $chartMetrics['unread_messages'] = $unreadMessages;
            if ($user->hasRole('Location Admin')) {
                $todayAssignmentsCount = ShiftAssignment::whereDate('date', $today)
                    ->where('location_id', $locationId)
                    ->count();
                $absencesTodayCount = EmployeeAbsence::whereDate('date', $today)->where('location_id', $locationId)->count();
                $recentAssignments = ShiftAssignment::with(['user','shift'])
                    ->where('location_id', $locationId)
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

        // Basic KPI (default 30 days): lateness, attendance rate, overtime, task productivity
        $kpiDays = (int) $request->get('kpi_days', 30);
        if ($kpiDays < 7) {
            $kpiDays = 7;
        } elseif ($kpiDays > 365) {
            $kpiDays = 365;
        }
        $kpiStart = now()->subDays($kpiDays - 1)->startOfDay();
        $kpiEnd = now()->endOfDay();
        $daysInPeriod = max(1, $kpiStart->diffInDays($kpiEnd) + 1);

        if ($user->hasRole('Super Admin')) {
            $totalTasksAll = Task::count();
            $completedTasksAll = Task::where('status', 'completed')->count();
            $locationMetrics['task_completion_rate'] = $totalTasksAll > 0 ? round(($completedTasksAll / $totalTasksAll) * 100, 2) : 0.0;

            $totalEmployeesAll = User::role('Employee')->count();
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

            $totalEmployeesLoc = User::role('Employee')->where('location_id', $locationId)->count();
            $checkedInTodayLoc = Attendance::whereDate('check_in_time', $today)
                ->whereHas('user', function ($q) use ($locationId) { $q->where('location_id', $locationId); })
                ->distinct('user_id')->count('user_id');
            $locationMetrics['attendance_rate_today'] = $totalEmployeesLoc > 0 ? round(($checkedInTodayLoc / $totalEmployeesLoc) * 100, 2) : 0.0;

            $locationMetrics['overtime_hours_30d'] = (float) Overtime::whereDate('date', '>=', now()->subDays(30)->toDateString())
                ->whereHas('user', function ($q) use ($locationId) { $q->where('location_id', $locationId); })
                ->sum('duration_hours');
        }

        $chartMetrics['overtime_hours_30d'] = $locationMetrics['overtime_hours_30d'];

        $attendanceQuery = Attendance::whereBetween('check_in_time', [$kpiStart, $kpiEnd]);
        $taskBaseQuery = Task::query();
        $overtimeQuery = Overtime::where('status', 'approved')->whereBetween('date', [$kpiStart->toDateString(), $kpiEnd->toDateString()]);

        if ($user->hasRole('Super Admin')) {
            $totalEmployeesForKpi = User::role('Employee')->count();
        } elseif ($user->hasRole('Location Admin')) {
            $locationId = $user->location_id;
            $attendanceQuery->whereHas('user', function ($q) use ($locationId) { $q->where('location_id', $locationId); });
            $taskBaseQuery->whereHas('assignee', function ($q) use ($locationId) { $q->where('location_id', $locationId); });
            $overtimeQuery->whereHas('user', function ($q) use ($locationId) { $q->where('location_id', $locationId); });
            $totalEmployeesForKpi = User::role('Employee')->where('location_id', $locationId)->count();
        } else {
            $attendanceQuery->where('user_id', $user->id);
            $taskBaseQuery->where('assigned_to', $user->id);
            $overtimeQuery->where('user_id', $user->id);
            $totalEmployeesForKpi = 1;
        }

        $attendanceCount = (int) $attendanceQuery->count();
        $lateCount = (int) (clone $attendanceQuery)->where('is_late', true)->count();
        $latenessRate = $attendanceCount > 0 ? round(($lateCount / $attendanceCount) * 100, 2) : 0.0;
        $attendanceRate30d = $totalEmployeesForKpi > 0
            ? round(min(100, ($attendanceCount / ($totalEmployeesForKpi * $daysInPeriod)) * 100), 2)
            : 0.0;

        $overtimeHours30d = (float) $overtimeQuery->sum('duration_hours');

        $tasksCreated = (int) (clone $taskBaseQuery)->whereBetween('created_at', [$kpiStart, $kpiEnd])->count();
        $tasksCompleted = (int) (clone $taskBaseQuery)->where('status', 'completed')->whereBetween('updated_at', [$kpiStart, $kpiEnd])->count();
        $taskProductivityRate = $tasksCreated > 0 ? round(($tasksCompleted / $tasksCreated) * 100, 2) : 0.0;
        $kpiMetrics = [
            'lateness_rate' => $latenessRate,
            'late_count' => $lateCount,
            'attendance_rate_30d' => $attendanceRate30d,
            'attendance_count' => $attendanceCount,
            'overtime_hours_30d' => $overtimeHours30d,
            'tasks_created' => $tasksCreated,
            'tasks_completed' => $tasksCompleted,
            'task_productivity_rate' => $taskProductivityRate,
            'period_label' => $kpiStart->format('d M') . ' - ' . $kpiEnd->format('d M'),
        ];

        $locationLabels = collect();
        $employeeCountsByLocation = collect();
        $userCountsByLocation = collect();
        if ($user->hasRole('Super Admin')) {
            $locations = Location::orderBy('name')->get(['id', 'name']);
            $employeeCountsByLocation = User::role('Employee')
                ->select('location_id', DB::raw('count(*) as total'))
                ->whereNotNull('location_id')
                ->groupBy('location_id')
                ->pluck('total', 'location_id');
            $userCountsByLocation = User::select('location_id', DB::raw('count(*) as total'))
                ->whereNotNull('location_id')
                ->groupBy('location_id')
                ->pluck('total', 'location_id');
        } elseif ($user->location_id) {
            $locations = Location::where('id', $user->location_id)->get(['id', 'name']);
            $employeeCountsByLocation = User::role('Employee')
                ->select('location_id', DB::raw('count(*) as total'))
                ->where('location_id', $user->location_id)
                ->groupBy('location_id')
                ->pluck('total', 'location_id');
            $userCountsByLocation = User::select('location_id', DB::raw('count(*) as total'))
                ->where('location_id', $user->location_id)
                ->groupBy('location_id')
                ->pluck('total', 'location_id');
        } else {
            $locations = collect();
        }

        $locationLabels = $locations->pluck('name');
        $employeeCountsByLocation = $locations->map(function ($loc) use ($employeeCountsByLocation) {
            return (int) ($employeeCountsByLocation[$loc->id] ?? 0);
        });
        $userCountsByLocation = $locations->map(function ($loc) use ($userCountsByLocation) {
            return (int) ($userCountsByLocation[$loc->id] ?? 0);
        });

        if ($user->hasRole('Employee')) {
            $chartMetrics['total_employees'] = 1;
            $chartMetrics['attendance_total'] = 1;
            $chartMetrics['checked_in_today'] = Attendance::where('user_id', $user->id)
                ->whereDate('check_in_time', $today)
                ->exists() ? 1 : 0;
            $chartMetrics['total_tasks'] = Task::where('assigned_to', $user->id)->count();
            $chartMetrics['completed_tasks'] = Task::where('assigned_to', $user->id)->where('status', 'completed')->count();

            $todayAssignment = ShiftAssignment::with(['shift', 'locationShift.shift'])
                ->where('user_id', $user->id)
                ->whereDate('date', $today)
                ->first();
            $todayRosterEntry = null;
            if ($todayAssignment) {
                $todayRosterEntry = \App\Models\WeeklyRosterEntry::where('user_id', $user->id)
                    ->whereDate('date', $today)
                    ->where('shift_assignment_id', $todayAssignment->id)
                    ->orderByDesc('id') // use the latest roster entry if duplicates exist
                    ->first();
            }
            if (!$todayRosterEntry) {
                $todayRosterEntry = \App\Models\WeeklyRosterEntry::where('user_id', $user->id)
                    ->whereDate('date', $today)
                    ->orderByDesc('id') // prefer the newest roster entry
                    ->first();
            }
            $isOffToday = $todayRosterEntry && $todayRosterEntry->status === 'off';
            $slotIndex = $isOffToday ? null : ($todayRosterEntry->slot_index ?? null);
            $todayAssignmentTime = $this->formatAssignmentTime($todayAssignment, $slotIndex);
            $todayPlannedDate = $isOffToday
                ? null
                : ($todayRosterEntry?->date ?? $todayAssignment?->date);
            if (!$isOffToday && WorkdayService::isWorkingDay($user, now()) === false) {
                $isOffToday = true;
                $todayAssignmentTime = __('Your holiday');
                $todayAssignment = null;
                $slotIndex = null;
            }
            // Fallback: jika belum ada assignment hari ini, gunakan default shift lokasi
            if (!$todayAssignmentTime && $user->location_id) {
                $fallbackShift = LocationShift::with('shift')
                    ->where('location_id', $user->location_id)
                    ->where(function ($q) {
                        $q->where('is_default', true)->orWhereNotNull('time_slots');
                    })
                    ->orderByDesc('is_default')
                    ->orderBy('id')
                    ->first();
                if ($fallbackShift) {
                    $todayAssignmentTime = $this->formatLocationShiftTime($fallbackShift, $slotIndex);
                    $todayPlannedDate = $todayPlannedDate ?? now();
                }
            }
            if ($isOffToday) {
                $todayAssignmentTime = __('Your holiday');
                $todayAssignment = null;
            }
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
        } else {
            $todayAssignmentTime = null;
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
                    $notices[] = ($h->is_national ? __('National Holiday') : __('Local Holiday')) . ': ' . $h->name;
                }
                if (!$holidaysToday->count() && WorkdayService::isWeeklyOff($user, now())) {
                    $notices[] = __('Weekly Off');
                }
                $onLeave = EmployeeLeave::where('user_id', $user->id)->where('status', 'approved')
                    ->whereDate('start_date', '<=', $today)->whereDate('end_date', '>=', $today)->first();
                if ($onLeave) {
                    $notices[] = __('Leave/Absence') . ' (' . $onLeave->type . ')';
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
                    $labels[] = ($h->is_national ? __('National Holiday') : __('Local Holiday')) . ': ' . $h->name;
                }
                if (WorkdayService::isWeeklyOff($user, $d)) {
                    $labels[] = __('Weekly Off');
                }
                $onLeave = EmployeeLeave::where('user_id', $user->id)->where('status', 'approved')
                    ->whereDate('start_date', '<=', $dateStr)->whereDate('end_date', '>=', $dateStr)->exists();
                if ($onLeave) {
                    $labels[] = __('Leave/Absence');
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

        // Get today's attendance for the user (and for Location Admin themselves)
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('check_in_time', now()->toDateString())
            ->first();

        $userLocationName = $user->location->name ?? '-';

        return view('dashboard', compact(
            'user', 'tasks', 'unreadMessages',
            'totalMasters', 'totalEmployees', 'totalTasks', 'totalMessages', 'totalUsers', 'totalDivisions', 'totalEmployees',
          'todayAttendance', 'todayAssignment', 'todayAssignmentsCount', 'recentAssignments', 'myUpcomingAssignments', 'locationMetrics', 'kpiMetrics', 'kpiDays', 'chartMetrics', 'todayNotices', 'upcomingNotices', 'absencesTodayCount',
          'locationLabels', 'employeeCountsByLocation', 'userCountsByLocation'
        ))->with('attendanceList', $attendanceList)
          ->with('todayAssignmentTime', $todayAssignmentTime)
          ->with('todayPlannedDate', optional($todayPlannedDate)->format('Y-m-d'))
          ->with('userLocationName', $userLocationName);
    }

    private function formatAssignmentTime(?ShiftAssignment $assignment, ?int $slotIndex = null): ?string
    {
        if (!$assignment) {
            return null;
        }
        $slots = $assignment->locationShift?->normalizedSlots() ?? [];
        if (empty($slots)) {
            $slots = $assignment->shift?->normalizedSlots() ?? [];
        }
        if (empty($slots)) {
            return null;
        }
        $slot = $slotIndex !== null && isset($slots[$slotIndex])
            ? $slots[$slotIndex]
            : $slots[0];
        if (empty($slot['start']) || empty($slot['end'])) {
            return null;
        }
        return $slot['start'] . ' - ' . $slot['end'];
    }

    private function formatLocationShiftTime(?LocationShift $locationShift, ?int $slotIndex = null): ?string
    {
        if (!$locationShift) {
            return null;
        }
        $slots = $locationShift->normalizedSlots();
        if (empty($slots)) {
            return null;
        }
        $slot = $slotIndex !== null && isset($slots[$slotIndex])
            ? $slots[$slotIndex]
            : $slots[0];
        if (empty($slot['start']) || empty($slot['end'])) {
            return null;
        }
        return $slot['start'] . ' - ' . $slot['end'];
    }
}
