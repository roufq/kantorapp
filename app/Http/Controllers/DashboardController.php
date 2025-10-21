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

        // Get counts for dashboard (location-aware for non Super Admin)
        if ($user->hasRole('Super Admin')) {
            $totalMasters = User::role('Super Admin')->count();
            $totalEmployees = User::role('Karyawan')->count();
            $totalTasks = Task::count();
            $totalMessages = Message::count();
            $totalUsers = User::count();
            $totalDivisions = Division::count();
            $totalKaryawans = Employee::count();
        } else {
            $locationId = $user->location_id;
            $totalMasters = User::role('Super Admin')->count(); // global masters
            $totalEmployees = User::role('Karyawan')->where('location_id', $locationId)->count();
            $totalTasks = Task::whereHas('assignee', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            })->count();
            $totalMessages = Message::whereHas('receiver', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            })->orWhereHas('sender', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            })->count();
            $totalUsers = User::where('location_id', $locationId)->count();
            $totalDivisions = Division::count(); // divisions not location-specific yet
            $totalKaryawans = Employee::where('location_id', $locationId)->count();
        }

        // Get today's attendance for the user
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('check_in_time', now()->toDateString())
            ->first();

        return view('dashboard', compact('user', 'tasks', 'unreadMessages', 'totalMasters', 'totalEmployees', 'totalTasks', 'totalMessages', 'totalUsers', 'totalDivisions', 'totalKaryawans', 'todayAttendance'));
    }
}
