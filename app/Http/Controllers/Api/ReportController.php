<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function attendance(Request $request)
    {
        $user = $request->user();

        // Authorization
        if (!$user->hasRole(['Super Admin', 'Location Admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = Attendance::with(['user', 'location', 'shift']);

        // Scope by role
        if ($user->hasRole('Location Admin')) {
            $query->where('location_id', $user->location_id);
        }

        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Summary stats
        $totalRecords = $query->count();
        $presentCount = (clone $query)->where('status', 'present')->count();
        $absentCount = (clone $query)->where('status', 'absent')->count();
        $lateCount = (clone $query)->where('is_late', true)->count();

        $records = $query->orderBy('date', 'desc')
                         ->orderBy('check_in_time', 'desc')
                         ->paginate(50);

        return response()->json([
            'summary' => [
                'total_records' => $totalRecords,
                'present' => $presentCount,
                'absent' => $absentCount,
                'late' => $lateCount,
                'attendance_rate' => $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 2) : 0
            ],
            'records' => $records
        ]);
    }

    public function overtime(Request $request)
    {
        $user = $request->user();

        // Authorization
        if (!$user->hasRole(['Super Admin', 'Location Admin'])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $query = Overtime::with(['user', 'approver']);

        // Scope by role
        if ($user->hasRole('Location Admin')) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('location_id', $user->location_id);
            });
        }

        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        // Summary stats
        $totalRecords = $query->count();
        $approvedCount = (clone $query)->where('status', 'approved')->count();
        $pendingCount = (clone $query)->where('status', 'pending')->count();
        $rejectedCount = (clone $query)->where('status', 'rejected')->count();

        $totalHours = (clone $query)->where('status', 'approved')->sum('hours');

        $records = $query->orderBy('date', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate(50);

        return response()->json([
            'summary' => [
                'total_records' => $totalRecords,
                'approved' => $approvedCount,
                'pending' => $pendingCount,
                'rejected' => $rejectedCount,
                'total_approved_hours' => round($totalHours, 2)
            ],
            'records' => $records
        ]);
    }

    public function userAttendance(Request $request, User $targetUser)
    {
        $user = $request->user();

        // Authorization
        if ($user->hasRole('Super Admin')) {
            // Can view anyone
        } elseif ($user->hasRole('Location Admin')) {
            if ($targetUser->location_id !== $user->location_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        } else {
            if ($targetUser->id !== $user->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        $query = Attendance::where('user_id', $targetUser->id)
                          ->with(['location', 'shift']);

        // Date range filter
        $dateFrom = $request->date_from ? Carbon::parse($request->date_from) : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to) : Carbon::now()->endOfMonth();

        $query->whereBetween('date', [$dateFrom, $dateTo]);

        $records = $query->orderBy('date', 'desc')->get();

        // Calculate summary
        $totalDays = $dateFrom->diffInDays($dateTo) + 1;
        $presentDays = $records->where('status', 'present')->count();
        $absentDays = $records->where('status', 'absent')->count();
        $lateDays = $records->where('is_late', true)->count();
        $totalWorkingHours = $records->sum('working_hours');

        return response()->json([
            'user' => $targetUser->only(['id', 'name', 'email']),
            'period' => [
                'from' => $dateFrom->format('Y-m-d'),
                'to' => $dateTo->format('Y-m-d')
            ],
            'summary' => [
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'late_days' => $lateDays,
                'total_working_hours' => round($totalWorkingHours, 2),
                'attendance_rate' => $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0
            ],
            'records' => $records
        ]);
    }
}
