<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Task;
use App\Models\User;
use App\Models\TaskSlot;
use App\Models\TaskSlotHistory;

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
            'duration_minutes' => 'nullable|integer|min:1',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'document' => 'nullable|file|mimes:pdf,doc,docx,txt|max:5120',
            'slots' => 'required|array|min:1',
            'slots.*.name' => 'required|string|max:255',
            'slots.*.percentage' => 'required|numeric|min:0.01|max:100',
            'slots.*.minutes' => 'required|integer|min:1',
            'slots.*.order' => 'nullable|integer|min:0|max:255',
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

        $slots = $request->input('slots', []);
        $totalPercent = collect($slots)->sum(fn($s) => (float) ($s['percentage'] ?? 0));
        $totalPercent = round($totalPercent, 2);
        if (abs($totalPercent - 100) <= 0.05) {
            $totalPercent = 100.0;
        }
        if (abs($totalPercent - 100) > 0.01) {
            return back()->withErrors(['slots' => 'Total persentase slot harus 100% (saat ini: ' . $totalPercent . '%).'])->withInput();
        }
        $totalMinutes = collect($slots)->sum(fn($s) => (int) ($s['minutes'] ?? 0));
        if ($request->duration_minutes && $totalMinutes !== (int) $request->duration_minutes) {
            return back()->withErrors(['slots' => 'Total menit slot harus sama dengan durasi task (' . $request->duration_minutes . ' menit). Saat ini: ' . $totalMinutes . ' menit.'])->withInput();
        }

        $assignee = User::findOrFail($request->assigned_to);
        $approvalMeta = Task::determineCreationApproval($user, $assignee);

        $task = Task::create(array_merge([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_by' => $user->id,
            'assigned_to' => $request->assigned_to,
            'status' => 'pending',
            'progress' => 0,
            'due_date' => $request->due_date,
            'duration_minutes' => $request->duration_minutes,
            'photo_path' => $photoPath,
            'document_path' => $documentPath,
        ], $approvalMeta));

        foreach ($slots as $idx => $slot) {
            $newSlot = TaskSlot::create([
                'task_id' => $task->id,
                'name' => $slot['name'],
                'percentage' => round((float) $slot['percentage'], 2),
                'minutes' => (int) $slot['minutes'],
                'order' => isset($slot['order']) ? (int) $slot['order'] : $idx,
                'created_by' => $user->id,
                'status' => 'pending',
            ]);
            TaskSlotHistory::create([
                'task_slot_id' => $newSlot->id,
                'action' => 'created',
                'data_after' => $newSlot->toArray(),
                'actor_id' => $user->id,
            ]);
        }

        $message = ($approvalMeta['requires_approval'] ?? false)
            ? 'Task dikirim untuk persetujuan.'
            : 'Task created!';

        return redirect()->route('location-admin-tasks.index')->with('success', $message);
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
            'due_date' => $request->due_date,
            'photo_path' => $photoPath,
            'document_path' => $documentPath,
        ]);

        $task->applyProgress((int) $task->progress);

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
