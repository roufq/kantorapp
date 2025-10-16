<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\Message;
use App\Models\Division;
use App\Models\Employee;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Fetch tasks based on role with search
        if ($user->role === 'master') {
            // Masters only see tasks they assigned or assigned to them
            $query = Task::where(function ($q) use ($user) {
                $q->where('assigned_by', $user->id)
                  ->orWhere('assigned_to', $user->id);
            })->with('assignee', 'assigner');
        } else {
            // Employees see only their own tasks
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

        // Get counts for dashboard
        $totalMasters = \App\Models\User::where('role', 'master')->count();
        $totalEmployees = \App\Models\User::where('role', 'employee')->count();
        $totalTasks = Task::count();
        $totalMessages = Message::count();
        $totalUsers = \App\Models\User::count();
        $totalDivisions = Division::count();
        $totalKaryawans = Employee::count();

        return view('dashboard', compact('user', 'tasks', 'unreadMessages', 'totalMasters', 'totalEmployees', 'totalTasks', 'totalMessages', 'totalUsers', 'totalDivisions', 'totalKaryawans'));
    }
}
