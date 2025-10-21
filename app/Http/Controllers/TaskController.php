<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Task;
use App\Models\User;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            // Super Admin: all tasks
            $query = Task::with('assignee', 'assigner');
        } elseif ($user->hasRole('Admin Lokasi')) {
            // Admin Lokasi: tasks for users in same location
            $locationId = $user->location_id;
            $query = Task::whereHas('assignee', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            })->with('assignee', 'assigner');
        } else {
            // Karyawan: own tasks
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

        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            $users = User::role('Karyawan')->get();
            return view('tasks.create', compact('users'));
        } elseif ($user->hasRole('Admin Lokasi')) {
            $users = User::role('Karyawan')->where('location_id', $user->location_id)->get();
            return view('tasks.create', compact('users'));
        }

        // Employees cannot create tasks for others, redirect to create-self
        return redirect()->route('tasks.create.self');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin') || $user->hasRole('Admin Lokasi')) {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'assigned_to' => 'required|exists:users,id',
                'due_date' => 'nullable|date|after:today',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'document' => 'nullable|file|mimes:pdf,doc,docx,txt|max:5120',
            ]);

            if ($user->hasRole('Admin Lokasi')) {
                $valid = User::role('Karyawan')
                    ->where('id', $request->assigned_to)
                    ->where('location_id', $user->location_id)
                    ->exists();
                if (!$valid) {
                    abort(403, 'You can only assign tasks to employees in your location');
                }
            }

            $photoPath = null;
            $documentPath = null;

            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('tasks/photos', 'public');
            }

            if ($request->hasFile('document')) {
                $documentPath = $request->file('document')->store('tasks/documents', 'public');
            }

            Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'assigned_by' => $user->id,
                'assigned_to' => $request->assigned_to,
                'due_date' => $request->due_date,
                'photo_path' => $photoPath,
                'document_path' => $documentPath,
            ]);
        } else {
            abort(403, 'Employees cannot assign tasks to others');
        }

        return redirect()->route('tasks.index')->with('success', 'Task created!');
    }

    public function show(Task $task)
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            return view('tasks.show', compact('task'));
        }
        if ($user->hasRole('Admin Lokasi')) {
            $assignee = $task->assignee;
            if ($assignee && $assignee->location_id === $user->location_id) {
                return view('tasks.show', compact('task'));
            }
            abort(403, 'Not authorized for this task');
        }
        // Karyawan: only own tasks
        if ($task->assigned_to !== $user->id) {
            abort(403, 'You can only access your own tasks');
        }
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            $users = User::role('Karyawan')->get();
            return view('tasks.edit', compact('task', 'users'));
        } elseif ($user->hasRole('Admin Lokasi')) {
            $assignee = $task->assignee;
            if ($assignee && $assignee->location_id === $user->location_id) {
                $users = User::role('Karyawan')->where('location_id', $user->location_id)->get();
                return view('tasks.edit', compact('task', 'users'));
            }
            abort(403, 'Not authorized to edit this task');
        } else {
            // Employees can only edit their own tasks, but only status
            if ($task->assigned_to !== $user->id) {
                abort(403, 'You can only edit your own tasks');
            }
            return view('tasks.edit', compact('task'));
        }
    }

    public function update(Request $request, Task $task)
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin') || $user->hasRole('Admin Lokasi')) {
            // Super Admin/Admin Lokasi can update all fields (Admin Lokasi within location)
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'assigned_to' => 'required|exists:users,id',
                'status' => 'required|in:pending,in_progress,completed',
                'due_date' => 'nullable|date',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'document' => 'nullable|file|mimes:pdf,doc,docx,txt|max:5120',
            ]);

            if ($user->hasRole('Admin Lokasi')) {
                // Ensure current task and target assignee are in the same location
                $assignee = $task->assignee;
                if (!$assignee || $assignee->location_id !== $user->location_id) {
                    abort(403, 'Not authorized to update this task');
                }
                $valid = User::role('Karyawan')
                    ->where('id', $request->assigned_to)
                    ->where('location_id', $user->location_id)
                    ->exists();
                if (!$valid) {
                    abort(403, 'You can only reassign within your location');
                }
            }

            $photoPath = $task->photo_path;
            $documentPath = $task->document_path;

            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($photoPath) {
                    Storage::disk('public')->delete($photoPath);
                }
                $photoPath = $request->file('photo')->store('tasks/photos', 'public');
            }

            if ($request->hasFile('document')) {
                // Delete old document if exists
                if ($documentPath) {
                    Storage::disk('public')->delete($documentPath);
                }
                $documentPath = $request->file('document')->store('tasks/documents', 'public');
            }

            $task->update([
                'title' => $request->title,
                'description' => $request->description,
                'assigned_to' => $request->assigned_to,
                'status' => $request->status,
                'due_date' => $request->due_date,
                'photo_path' => $photoPath,
                'document_path' => $documentPath,
            ]);
        } else {
            // Employees can only update status
            if ($task->assigned_to !== $user->id) {
                abort(403, 'You can only update your own tasks');
            }

            $request->validate([
                'status' => 'required|in:pending,in_progress,completed',
            ]);

            $task->update([
                'status' => $request->status,
            ]);
        }

        return redirect()->route('tasks.index')->with('success', 'Task updated!');
    }

    public function destroy(Task $task)
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            // Super Admin can delete all tasks
            // Delete associated files
            if ($task->photo_path) {
                Storage::disk('public')->delete($task->photo_path);
            }
            if ($task->document_path) {
                Storage::disk('public')->delete($task->document_path);
            }
            $task->delete();
        } elseif ($user->hasRole('Admin Lokasi')) {
            $assignee = $task->assignee;
            if ($assignee && $assignee->location_id === $user->location_id) {
                if ($task->photo_path) {
                    Storage::disk('public')->delete($task->photo_path);
                }
                if ($task->document_path) {
                    Storage::disk('public')->delete($task->document_path);
                }
                $task->delete();
            } else {
                abort(403, 'Not authorized to delete this task');
            }
        } else {
            // Employees can only delete their own tasks
            if ($task->assigned_to !== $user->id) {
                abort(403, 'You can only delete your own tasks');
            }
            // Delete associated files
            if ($task->photo_path) {
                Storage::disk('public')->delete($task->photo_path);
            }
            if ($task->document_path) {
                Storage::disk('public')->delete($task->document_path);
            }
            $task->delete();
        }

        return redirect()->route('tasks.index')->with('success', 'Task deleted!');
    }

    public function createSelf()
    {
        return view('tasks.create-self');
    }

    public function storeSelf(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date|after:today',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'document' => 'nullable|file|mimes:pdf,doc,docx,txt|max:5120',
        ]);

        $photoPath = null;
        $documentPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('tasks/photos', 'public');
        }

        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('tasks/documents', 'public');
        }

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_by' => $user->id,
            'assigned_to' => $user->id,
            'due_date' => $request->due_date,
            'photo_path' => $photoPath,
            'document_path' => $documentPath,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task created for yourself!');
    }

    public function downloadPhoto(Task $task)
    {
        $user = Auth::user();
        if ($user->hasRole('Super Admin') || $task->assigned_to === $user->id || ($user->hasRole('Admin Lokasi') && optional($task->assignee)->location_id === $user->location_id)) {
            if (!$task->photo_path || !Storage::disk('public')->exists($task->photo_path)) {
                abort(404);
            }
            return Storage::disk('public')->download($task->photo_path);
        }
        abort(403);
    }

    public function downloadDocument(Task $task)
    {
        $user = Auth::user();
        if ($user->hasRole('Super Admin') || $task->assigned_to === $user->id || ($user->hasRole('Admin Lokasi') && optional($task->assignee)->location_id === $user->location_id)) {
            if (!$task->document_path || !Storage::disk('public')->exists($task->document_path)) {
                abort(404);
            }
            return Storage::disk('public')->download($task->document_path);
        }
        abort(403);
    }
}
