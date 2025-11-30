<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Task;
use App\Models\User;

class LocationAdminTaskController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $this->authorizeAccess($user);
        // Clear any stale forbidden flash so the page doesn't always show modal after a prior 403
        if (session()->has('forbidden')) {
            session()->forget('forbidden');
        }

        if ($user->hasRole('Super Admin')) {
            $query = Task::with('assignee', 'assigner');
        } else {
            $locationId = $user->location_id;
            $query = Task::whereHas('assignee', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            })->with('assignee', 'assigner');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhereHas('assignee', function ($subQ) use ($search) {
                      $subQ->where('name', 'like', "%$search%");
                  });
            });
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('location-admin-tasks.index', compact('tasks'));
    }

    public function create()
    {
        $user = Auth::user();
        $this->authorizeAccess($user);

        // Admin Lokasi: dapat menetapkan ke Karyawan maupun Admin Lokasi di lokasi yang sama
        if ($user->hasRole('Super Admin')) {
            $users = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['Karyawan', 'Admin Lokasi']);
            })->get();
        } else {
            $users = User::where('location_id', $user->location_id)
                ->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['Karyawan', 'Admin Lokasi']);
                })->get();
        }
        return view('location-admin-tasks.create', compact('users'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $this->authorizeAccess($user);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'nullable|date|after_or_equal:today',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'document' => 'nullable|file|mimes:pdf,doc,docx,txt|max:5120',
        ]);

        // ensure assignee within location (Karyawan/Admin Lokasi)
        if (!$user->hasRole('Super Admin')) {
            $valid = User::where('id', $request->assigned_to)
                ->where('location_id', $user->location_id)
                ->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['Karyawan', 'Admin Lokasi']);
                })
                ->exists();
            if (!$valid) {
                abort(403, 'You can only assign tasks to employees in your location');
            }
        }

        $photoPath = $request->hasFile('photo') ? $request->file('photo')->store('tasks/photos', 'public') : null;
        $documentPath = $request->hasFile('document') ? $request->file('document')->store('tasks/documents', 'public') : null;

        Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_by' => $user->id,
            'assigned_to' => $request->assigned_to,
            'due_date' => $request->due_date,
            'photo_path' => $photoPath,
            'document_path' => $documentPath,
        ]);

        return redirect()->route('location-admin-tasks.index')->with('success', 'Task created!');
    }

    public function show(Task $task)
    {
        $user = Auth::user();
        $this->authorizeAccess($user);
        if (!$user->hasRole('Super Admin')) {
            if (!optional($task->assignee)->location_id || $task->assignee->location_id !== $user->location_id) {
                abort(403);
            }
        }
        return view('location-admin-tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $user = Auth::user();
        $this->authorizeAccess($user);
        if (!$user->hasRole('Super Admin')) {
            if (!optional($task->assignee)->location_id || $task->assignee->location_id !== $user->location_id) {
                abort(403);
            }
        }
        if ($user->hasRole('Super Admin')) {
            $users = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['Karyawan', 'Admin Lokasi']);
            })->get();
        } else {
            $users = User::where('location_id', $user->location_id)
                ->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['Karyawan', 'Admin Lokasi']);
                })->get();
        }
        return view('location-admin-tasks.edit', compact('task', 'users'));
    }

    public function update(Request $request, Task $task)
    {
        $user = Auth::user();
        $this->authorizeAccess($user);
        if (!$user->hasRole('Super Admin')) {
            if (!optional($task->assignee)->location_id || $task->assignee->location_id !== $user->location_id) {
                abort(403);
            }
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date|after_or_equal:today',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'document' => 'nullable|file|mimes:pdf,doc,docx,txt|max:5120',
        ]);

        if (!$user->hasRole('Super Admin')) {
            $valid = User::where('location_id', $user->location_id)
                ->where('id', $request->assigned_to)
                ->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['Karyawan', 'Admin Lokasi']);
                })
                ->exists();
            if (!$valid) {
                abort(403, 'You can only reassign within your location');
            }
        }

        $photoPath = $task->photo_path;
        $documentPath = $task->document_path;
        if ($request->hasFile('photo')) {
            if ($photoPath) { Storage::disk('public')->delete($photoPath); }
            $photoPath = $request->file('photo')->store('tasks/photos', 'public');
        }
        if ($request->hasFile('document')) {
            if ($documentPath) { Storage::disk('public')->delete($documentPath); }
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

        return redirect()->route('location-admin-tasks.index')->with('success', 'Task updated!');
    }

    public function destroy(Task $task)
    {
        $user = Auth::user();
        $this->authorizeAccess($user);
        if (!$user->hasRole('Super Admin')) {
            if (!optional($task->assignee)->location_id || $task->assignee->location_id !== $user->location_id) {
                abort(403);
            }
        }
        if ($task->photo_path) { Storage::disk('public')->delete($task->photo_path); }
        if ($task->document_path) { Storage::disk('public')->delete($task->document_path); }
        $task->delete();
        return redirect()->route('location-admin-tasks.index')->with('success', 'Task deleted!');
    }

    public function downloadPhoto(Task $task)
    {
        $user = Auth::user();
        $this->authorizeAccess($user);
        if (!$user->hasRole('Super Admin')) {
            if (!optional($task->assignee)->location_id || $task->assignee->location_id !== $user->location_id) {
                abort(403);
            }
        }
        if (!$task->photo_path || !Storage::disk('public')->exists($task->photo_path)) {
            abort(404);
        }
        return Storage::disk('public')->download($task->photo_path);
    }

    public function downloadDocument(Task $task)
    {
        $user = Auth::user();
        $this->authorizeAccess($user);
        if (!$user->hasRole('Super Admin')) {
            if (!optional($task->assignee)->location_id || $task->assignee->location_id !== $user->location_id) {
                abort(403);
            }
        }
        if (!$task->document_path || !Storage::disk('public')->exists($task->document_path)) {
            abort(404);
        }
        return Storage::disk('public')->download($task->document_path);
    }

    private function authorizeAccess($user): void
    {
        if (!$user->hasRole('Admin Lokasi') && !$user->hasRole('Super Admin')) {
            abort(403);
        }
        if ($user->hasRole('Admin Lokasi') && !$user->location_id) {
            abort(403, 'Admin Lokasi requires an assigned location');
        }
    }
}
