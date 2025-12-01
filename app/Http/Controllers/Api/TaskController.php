<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('Super Admin')) {
            $query = Task::with('assignee', 'assigner');
        } elseif ($user->hasRole('Admin Lokasi')) {
            $locationId = $user->location_id;
            $query = Task::whereHas('assignee', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            })->with('assignee', 'assigner');
        } else {
            $query = Task::where('assigned_to', $user->id)->with('assignee', 'assigner');
        }

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhereHas('assignee', function ($subQ) use ($search) {
                      $subQ->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($tasks);
    }

    public function show(Request $request, Task $task)
    {
        $user = $request->user();

        if ($user->hasRole('Super Admin')) {
            return response()->json($task->load('assignee', 'assigner'));
        }

        if ($user->hasRole('Admin Lokasi')) {
            $assignee = $task->assignee;
            if ($assignee && $assignee->location_id === $user->location_id) {
                return response()->json($task->load('assignee', 'assigner'));
            }
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($task->assigned_to !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($task->load('assignee', 'assigner'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        if (!$user->hasRole('Super Admin') && !$user->hasRole('Admin Lokasi')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);

        // Authorization checks
        if ($user->hasRole('Admin Lokasi')) {
            if ((int)$request->assigned_to !== (int)$user->id) {
                $valid = \App\Models\User::where('id', $request->assigned_to)
                    ->where('location_id', $user->location_id)
                    ->whereHas('roles', function ($q) {
                        $q->whereIn('name', ['Karyawan', 'Admin Lokasi']);
                    })
                    ->exists();
                if (!$valid) {
                    return response()->json(['message' => 'Can only assign within location'], 403);
                }
            }
        }

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_by' => $user->id,
            'assigned_to' => $request->assigned_to,
            'status' => 'pending',
            'due_date' => $request->due_date,
        ]);

        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task)
    {
        $user = $request->user();

        $isStatusOnly = $request->has('status') && !$request->hasAny(['title', 'description', 'assigned_to', 'due_date']);

        if ($isStatusOnly) {
            if ($user->hasRole('Super Admin')) {
                // allowed
            } elseif ($user->hasRole('Admin Lokasi')) {
                if (optional($task->assignee)->location_id !== $user->location_id) {
                    return response()->json(['message' => 'Unauthorized'], 403);
                }
            } else {
                if ($task->assigned_to !== $user->id) {
                    return response()->json(['message' => 'Unauthorized'], 403);
                }
            }

            $request->validate(['status' => 'required|in:pending,in_progress,completed']);
            $task->update(['status' => $request->status]);

            return response()->json($task);
        }

        if (!$user->hasRole('Super Admin') && !$user->hasRole('Admin Lokasi')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date|after_or_equal:today',
        ]);

        // Authorization checks for Admin Lokasi
        if ($user->hasRole('Admin Lokasi')) {
            $assignee = $task->assignee;
            if (!$assignee || $assignee->location_id !== $user->location_id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            if ((int)$request->assigned_to !== (int)$user->id) {
                $valid = \App\Models\User::where('id', $request->assigned_to)
                    ->where('location_id', $user->location_id)
                    ->whereHas('roles', function ($q) {
                        $q->whereIn('name', ['Karyawan', 'Admin Lokasi']);
                    })
                    ->exists();
                if (!$valid) {
                    return response()->json(['message' => 'Can only reassign within location'], 403);
                }
            }
        }

        $task->update($request->only(['title', 'description', 'assigned_to', 'status', 'due_date']));

        return response()->json($task);
    }

    public function destroy(Request $request, Task $task)
    {
        $user = $request->user();

        if ($user->hasRole('Super Admin')) {
            $task->delete();
            return response()->json(['message' => 'Task deleted']);
        }

        if ($user->hasRole('Admin Lokasi')) {
            $assignee = $task->assignee;
            if ($assignee && $assignee->location_id === $user->location_id) {
                $task->delete();
                return response()->json(['message' => 'Task deleted']);
            }
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($task->assigned_to !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }
}
