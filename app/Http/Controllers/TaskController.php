<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Task;
use App\Models\User;
use App\Models\TaskSlot;
use App\Models\TaskSlotHistory;
use App\Notifications\TaskApprovalNotification;
use Illuminate\Support\Facades\DB;

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

        // Optional filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // Filter range tanggal (due_date)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('due_date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('due_date', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('due_date', '<=', $request->end_date);
        }
        // Super Admin can filter by location and role
        if ($user->hasRole('Super Admin')) {
            if ($request->filled('location_id')) {
                $locId = (int) $request->location_id;
                $query->whereHas('assignee', function ($q) use ($locId) {
                    $q->where('location_id', $locId);
                });
            }
            if ($request->filled('assignee_role')) {
                $role = $request->assignee_role;
                $query->whereHas('assignee', function ($q) use ($role) {
                    $q->role($role);
                });
            }
        }
        // Direct assignee filter
        if ($request->filled('assignee_id')) {
            $query->where('assigned_to', (int) $request->assignee_id);
        }

        $query->withCount([
            'slots as pending_slots_count' => function ($q) {
                $q->where('status', 'pending')->whereHas('attachments', function ($aq) {
                    $aq->where('type', 'link');
                });
            },
        ]);

        $tasks = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->hasRole('Super Admin')) {
            $users = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['Karyawan', 'Admin Lokasi']);
            })->get();
            // include self if not in list
            if (!$users->contains('id', $user->id)) {
                $users->push($user);
            }
            return view('tasks.create', compact('users'));
        } elseif ($user->hasRole('Admin Lokasi')) {
            // Admin Lokasi: bisa assign ke Karyawan atau Admin Lokasi di lokasi yang sama (atau diri sendiri)
            $users = User::where('location_id', $user->location_id)
                ->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['Karyawan', 'Admin Lokasi']);
                })
                ->get();
            if (!$users->contains('id', $user->id)) {
                $users->push($user);
            }
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
                'due_date' => 'nullable|date|after_or_equal:today',
                'duration_minutes' => 'nullable|integer|min:1',
                'slots' => 'required|array|min:1',
                'slots.*.name' => 'required|string|max:255',
                'slots.*.percentage' => 'required|numeric|min:0.01|max:100',
                'slots.*.minutes' => 'required|integer|min:1',
                'slots.*.order' => 'nullable|integer|min:0|max:255',
            ]);

            // Authorization rules
            $assignee = User::findOrFail($request->assigned_to);
            if ($user->hasRole('Admin Lokasi')) {
                // Admin Lokasi: assign ke Karyawan/Admin Lokasi di lokasi yg sama atau diri sendiri
                if ((int)$request->assigned_to !== (int)$user->id) {
                    $valid = User::where('id', $request->assigned_to)
                        ->where('location_id', $user->location_id)
                        ->whereHas('roles', function ($q) {
                            $q->whereIn('name', ['Karyawan', 'Admin Lokasi']);
                        })
                        ->exists();
                    if (!$valid) {
                        abort(403, 'You can only assign tasks within your location (employee or location admin) or to yourself');
                    }
                }
            } elseif ($user->hasRole('Super Admin')) {
                // Super Admin can assign to Karyawan/Admin Lokasi or themselves
                if ((int)$request->assigned_to !== (int)$user->id) {
                    $valid = User::whereHas('roles', function ($q) {
                        $q->whereIn('name', ['Karyawan', 'Admin Lokasi']);
                    })
                        ->where('id', $request->assigned_to)
                        ->exists();
                    if (!$valid) {
                        abort(403, 'Target must be Karyawan/Admin Lokasi or yourself');
                    }
                }
            }

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
            ], $approvalMeta));

            // Tambahkan slot awal jika diisi
            $slots = $request->input('slots', []);
            $totalPercent = collect($slots)->sum(fn($s) => (float) ($s['percentage'] ?? 0));
            $totalPercent = round($totalPercent, 2);
            if (abs($totalPercent - 100) <= 0.05) {
                $totalPercent = 100.0;
            }
            $totalMinutes = collect($slots)->sum(fn($s) => (int) ($s['minutes'] ?? 0));
            if (abs($totalPercent - 100) > 0.01) {
                return back()->withErrors(['slots' => 'Total persentase slot harus 100% (saat ini: ' . $totalPercent . '%).'])->withInput();
            }
            if ($task->duration_minutes && $totalMinutes !== (int) $task->duration_minutes) {
                return back()->withErrors(['slots' => 'Total menit slot harus sama dengan durasi task (' . $task->duration_minutes . ' menit). Saat ini: ' . $totalMinutes . ' menit.'])->withInput();
            }
            foreach ($slots as $idx => $slot) {
                $newSlot = TaskSlot::create([
                    'task_id' => $task->id,
                    'name' => $slot['name'],
                    'percentage' => round((float) $slot['percentage'], 2),
                    'minutes' => (int) $slot['minutes'],
                    'order' => isset($slot['order']) ? (int) $slot['order'] : $idx,
                    'created_by' => $user->id,
                    'status' => 'draft',
                ]);
                TaskSlotHistory::create([
                    'task_slot_id' => $newSlot->id,
                    'action' => 'created',
                    'data_after' => $newSlot->toArray(),
                    'actor_id' => $user->id,
                ]);
            }
        } else {
            abort(403, 'Employees cannot assign tasks to others');
        }

        $message = ($approvalMeta['requires_approval'] ?? false)
            ? 'Task dikirim dan menunggu persetujuan.'
            : 'Task created!';

        return redirect()->route('tasks.index')->with('success', $message);
    }

    public function show(Task $task)
    {
        $user = Auth::user();

        $task->load([
            'slots.attachments',
            'slots.approver',
            'slots.creator',
            'approver',
        ]);
        $progressUpdates = $task->progressUpdates()->with(['user', 'approver'])->orderBy('created_at', 'desc')->get();

        if ($user->hasRole('Super Admin')) {
            return view('tasks.show', compact('task', 'progressUpdates'));
        }
        if ($user->hasRole('Admin Lokasi')) {
            $assignee = $task->assignee;
            if ($assignee && $assignee->location_id === $user->location_id) {
                return view('tasks.show', compact('task', 'progressUpdates'));
            }
            abort(403, 'Not authorized for this task');
        }
        // Karyawan: only own tasks
        if ($task->assigned_to !== $user->id) {
            abort(403, 'You can only access your own tasks');
        }
        return view('tasks.show', compact('task', 'progressUpdates'));
    }

    public function edit(Task $task)
    {
        $user = Auth::user();
        $canSelfEditRejected = $task->assigned_to === $user->id && $task->approval_status === 'rejected';

        if ($user->hasRole('Super Admin')) {
            $users = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['Karyawan', 'Admin Lokasi']);
            })->get();
            if (!$users->contains('id', $user->id)) {
                $users->push($user);
            }
            return view('tasks.edit', compact('task', 'users', 'canSelfEditRejected'));
        } elseif ($user->hasRole('Admin Lokasi')) {
            $assignee = $task->assignee;
            if ($assignee && $assignee->location_id === $user->location_id) {
                // Admin Lokasi boleh pilih Karyawan/Admin Lokasi di lokasi yang sama
                $users = User::where('location_id', $user->location_id)
                    ->whereHas('roles', function ($q) {
                        $q->whereIn('name', ['Karyawan', 'Admin Lokasi']);
                    })
                    ->get();
                if (!$users->contains('id', $user->id)) {
                    $users->push($user);
                }
                return view('tasks.edit', compact('task', 'users', 'canSelfEditRejected'));
            }
            abort(403, 'Not authorized to edit this task');
        } else {
            if ($task->assigned_to !== $user->id) {
                abort(403, 'You can only edit your own tasks');
            }
            if (!$canSelfEditRejected) {
                return redirect()->route('tasks.show', $task)->with('info', 'Gunakan form update progres di halaman detail tugas.');
            }
            // Karyawan boleh mengedit jika tugasnya ditolak, untuk perbaikan
            $users = collect([$user]);
            return view('tasks.edit', compact('task', 'users', 'canSelfEditRejected'));
        }
    }

    public function update(Request $request, Task $task)
    {
        $user = Auth::user();
        $isSelfRejected = $task->assigned_to === $user->id && $task->approval_status === 'rejected';

        if (!$user->hasRole('Super Admin') && !$user->hasRole('Admin Lokasi') && !$isSelfRejected) {
            abort(403, 'Perubahan detail tugas hanya oleh admin.');
        }

        // Jalur khusus karyawan memperbaiki tugas yang ditolak
        if ($isSelfRejected && !$user->hasAnyRole(['Super Admin', 'Admin Lokasi'])) {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'nullable|date|after_or_equal:today',
                'duration_minutes' => 'nullable|integer|min:1',
                'slots' => 'required|array|min:1',
                'slots.*.name' => 'required|string|max:255',
                'slots.*.percentage' => 'required|numeric|min:0.01|max:100',
                'slots.*.minutes' => 'required|integer|min:1',
                'slots.*.order' => 'nullable|integer|min:0|max:255',
            ]);

            $slotsInput = array_values($request->input('slots', []));
            $totalPercent = collect($slotsInput)->sum(fn($s) => (float) ($s['percentage'] ?? 0));
            $totalPercent = round($totalPercent, 2);
            if (abs($totalPercent - 100) <= 0.05) {
                $totalPercent = 100.0;
            }
            if (abs($totalPercent - 100) > 0.01) {
                return back()->withErrors(['slots' => 'Total persentase slot harus 100% (saat ini: ' . $totalPercent . '%).'])->withInput();
            }
            $totalMinutes = collect($slotsInput)->sum(fn($s) => (int) ($s['minutes'] ?? 0));
            if ($request->duration_minutes && $totalMinutes !== (int) $request->duration_minutes) {
                return back()->withErrors(['slots' => 'Total menit slot harus sama dengan durasi task (' . $request->duration_minutes . ' menit). Saat ini: ' . $totalMinutes . ' menit.'])->withInput();
            }

            DB::transaction(function () use ($request, $task, $slotsInput, $user) {
                $task->update([
                    'title' => $request->title,
                    'description' => $request->description,
                    'due_date' => $request->due_date,
                    'duration_minutes' => $request->duration_minutes,
                    'approval_status' => 'pending',
                    'approved_by' => null,
                    'approved_at' => null,
                    // approval_note tetap berisi alasan penolakan terakhir sebagai referensi
                ]);

                $existingSlots = $task->slots()->orderBy('order')->get()->values();
                $usedSlotIds = [];

                foreach ($slotsInput as $idx => $slot) {
                    $slotModel = $existingSlots[$idx] ?? null;
                    if ($slotModel) {
                        $before = $slotModel->toArray();
                        $slotModel->update([
                            'name' => $slot['name'],
                            'percentage' => round((float) $slot['percentage'], 2),
                            'minutes' => (int) $slot['minutes'],
                            'order' => isset($slot['order']) ? (int) $slot['order'] : $idx,
                            'status' => 'pending',
                            'approved_by' => null,
                            'approved_at' => null,
                            'rejection_reason' => null,
                        ]);
                        TaskSlotHistory::create([
                            'task_slot_id' => $slotModel->id,
                            'action' => 'updated',
                            'data_before' => $before,
                            'data_after' => $slotModel->toArray(),
                            'actor_id' => $user->id,
                        ]);
                        $usedSlotIds[] = $slotModel->id;
                    } else {
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
                        $usedSlotIds[] = $newSlot->id;
                    }
                }

                // Hapus slot yang tidak dipakai lagi (dengan history)
                foreach ($existingSlots as $slotModel) {
                    if (in_array($slotModel->id, $usedSlotIds, true)) {
                        continue;
                    }
                    $before = $slotModel->toArray();
                    $slotModel->delete();
                    TaskSlotHistory::create([
                        'task_slot_id' => $slotModel->id,
                        'action' => 'deleted',
                        'data_before' => $before,
                        'actor_id' => $user->id,
                    ]);
                }

                $task->recalcProgressFromSlots();
            });

            $task->applyProgress((int) $task->progress);

            return redirect()->route('tasks.show', $task)->with('success', 'Tugas direvisi dan dikirim ulang untuk persetujuan.');
        }

        // Super Admin/Admin Lokasi can update task metadata (progress via progress updates)
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'due_date' => 'nullable|date|after_or_equal:today',
            'duration_minutes' => 'nullable|integer|min:1',
            'slots' => 'nullable|array',
            'slots.*.name' => 'required_with:slots|string|max:255',
            'slots.*.percentage' => 'required_with:slots|numeric|min:0.01|max:100',
            'slots.*.minutes' => 'required_with:slots|integer|min:1',
            'slots.*.order' => 'nullable|integer|min:0|max:255',
        ]);

        if ($user->hasRole('Admin Lokasi')) {
            // Ensure current task and target assignee are in the same location
            $assignee = $task->assignee;
            if (!$assignee || $assignee->location_id !== $user->location_id) {
                abort(403, 'Not authorized to update this task');
            }
            if ((int)$request->assigned_to !== (int)$user->id) {
                $valid = User::where('id', $request->assigned_to)
                    ->where('location_id', $user->location_id)
                    ->whereHas('roles', function ($q) {
                        $q->whereIn('name', ['Karyawan', 'Admin Lokasi']);
                    })
                    ->exists();
                if (!$valid) {
                    abort(403, 'You can only reassign within your location (employee or location admin) or to yourself');
                }
            }
        } elseif ($user->hasRole('Super Admin')) {
            if ((int)$request->assigned_to !== (int)$user->id) {
                $valid = User::whereHas('roles', function ($q) {
                    $q->whereIn('name', ['Karyawan', 'Admin Lokasi']);
                })
                    ->where('id', $request->assigned_to)
                    ->exists();
                if (!$valid) {
                    abort(403, 'Target must be Karyawan/Admin Lokasi or yourself');
                }
            }
        }

        DB::transaction(function () use ($request, $task, $user) {
            $task->update([
                'title' => $request->title,
                'description' => $request->description,
                'assigned_to' => $request->assigned_to,
                'due_date' => $request->due_date,
                'duration_minutes' => $request->duration_minutes,
            ]);

            $slots = $request->input('slots', []);
            if (!empty($slots)) {
                $totalPercent = collect($slots)->sum(fn($s) => (float) ($s['percentage'] ?? 0));
                $totalPercent = round($totalPercent, 2);
                if (abs($totalPercent - 100) <= 0.05) {
                    $totalPercent = 100.0;
                }
                $totalMinutes = collect($slots)->sum(fn($s) => (int) ($s['minutes'] ?? 0));
                if (abs($totalPercent - 100) > 0.01) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['slots' => 'Total persentase slot harus 100% (saat ini: ' . $totalPercent . '%).']);
                }
                if ($task->duration_minutes && $totalMinutes > $task->duration_minutes) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['slots' => 'Total menit slot melebihi durasi task.']);
                }

                // hapus slot lama (catat history)
                foreach ($task->slots as $oldSlot) {
                    TaskSlotHistory::create([
                        'task_slot_id' => $oldSlot->id,
                        'action' => 'deleted',
                        'data_before' => $oldSlot->toArray(),
                        'actor_id' => $user->id,
                    ]);
                    $oldSlot->delete();
                }

                // buat slot baru
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
                $task->recalcProgressFromSlots();
            }
        });

        // Pastikan status mengikuti progres terkini
        $task->applyProgress((int) $task->progress);

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
            foreach ($task->progressUpdates as $update) {
                if ($update->photo_path) {
                    Storage::disk('public')->delete($update->photo_path);
                }
                if ($update->document_path) {
                    Storage::disk('public')->delete($update->document_path);
                }
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
                foreach ($task->progressUpdates as $update) {
                    if ($update->photo_path) {
                        Storage::disk('public')->delete($update->photo_path);
                    }
                    if ($update->document_path) {
                        Storage::disk('public')->delete($update->document_path);
                    }
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
            foreach ($task->progressUpdates as $update) {
                if ($update->photo_path) {
                    Storage::disk('public')->delete($update->photo_path);
                }
                if ($update->document_path) {
                    Storage::disk('public')->delete($update->document_path);
                }
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
            'due_date' => 'nullable|date|after_or_equal:today',
            'duration_minutes' => 'nullable|integer|min:1',
            'slots' => 'required|array|min:1',
            'slots.*.name' => 'required|string|max:255',
            'slots.*.percentage' => 'required|numeric|min:0.01|max:100',
            'slots.*.minutes' => 'required|integer|min:1',
            'slots.*.order' => 'nullable|integer|min:0|max:255',
        ]);

        $approvalMeta = Task::determineCreationApproval($user, $user);

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

        $task = Task::create(array_merge([
            'title' => $request->title,
            'description' => $request->description,
            'assigned_by' => $user->id,
            'assigned_to' => $user->id,
            'status' => 'pending',
            'progress' => 0,
            'due_date' => $request->due_date,
            'duration_minutes' => $request->duration_minutes,
        ], $approvalMeta));

        foreach ($slots as $idx => $slot) {
            $newSlot = TaskSlot::create([
                'task_id' => $task->id,
                'name' => $slot['name'],
                'percentage' => round((float) $slot['percentage'], 2),
                'minutes' => (int) $slot['minutes'],
                'order' => isset($slot['order']) ? (int) $slot['order'] : $idx,
                'created_by' => $user->id,
                'status' => 'draft',
            ]);
            TaskSlotHistory::create([
                'task_slot_id' => $newSlot->id,
                'action' => 'created',
                'data_after' => $newSlot->toArray(),
                'actor_id' => $user->id,
            ]);
        }

        $message = $approvalMeta['approval_level'] === 'location_admin'
            ? 'Task dikirim ke Admin Lokasi untuk persetujuan.'
            : ($approvalMeta['requires_approval'] ? 'Task dikirim ke Super Admin untuk persetujuan.' : 'Task created for yourself!');

        return redirect()->route('tasks.index')->with('success', $message);
    }

    private function ensureCanApproveCreation(Task $task): void
    {
        if (!$task->requires_approval || $task->approval_status !== 'pending') {
            abort(403, 'Tugas ini tidak membutuhkan persetujuan atau sudah diproses.');
        }

        $user = Auth::user();

        if ($task->approval_level === 'super_admin') {
            if ($user->hasRole('Super Admin')) {
                return;
            }
            abort(403);
        }

        if ($task->approval_level === 'location_admin') {
            if ($user->hasRole('Super Admin')) {
                return;
            }
            if ($user->hasRole('Admin Lokasi') && optional($task->assignee)->location_id === $user->location_id) {
                return;
            }
        }

        abort(403);
    }

    public function approveCreation(Task $task)
    {
        $this->ensureCanApproveCreation($task);

        $task->update([
            'approval_status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_note' => null,
        ]);

        if ($task->assignee) {
            $task->assignee->notify(new TaskApprovalNotification($task, 'approved'));
        }

        return back()->with('success', 'Tugas disetujui.');
    }

    public function rejectCreation(Request $request, Task $task)
    {
        $this->ensureCanApproveCreation($task);

        $data = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        DB::transaction(function () use ($task, $data) {
            $task->update([
                'approval_status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'approval_note' => $data['reason'],
            ]);

            foreach ($task->slots as $slot) {
                $before = $slot->toArray();
                $slot->update([
                    'status' => 'rejected',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'rejection_reason' => $data['reason'],
                ]);
                TaskSlotHistory::create([
                    'task_slot_id' => $slot->id,
                    'action' => 'rejected',
                    'data_before' => $before,
                    'data_after' => $slot->toArray(),
                    'actor_id' => Auth::id(),
                ]);
            }
        });

        if ($task->assignee) {
            $task->assignee->notify(new TaskApprovalNotification($task, 'rejected'));
        }

        return back()->with('success', 'Tugas ditolak.');
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
