<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskProgressUpdate;
use App\Models\TaskSlot;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskProgressController extends Controller
{
    // [NEW] Method to show the progress update form
    public function create(Request $request, Task $task)
    {
        $user = Auth::user();

        if ($task->requires_approval && $task->approval_status !== 'approved') {
            return redirect()->route('tasks.show', $task)->with('error', 'Tugas ini belum disetujui, progres belum dapat diperbarui.');
        }

        if (!$task->slots()->exists()) {
            return redirect()->route('tasks.show', $task)->with('error', 'Tugas belum memiliki slot. Tambahkan slot progres terlebih dahulu.');
        }

        $slotStatus = $task->getSlotCompositionStatus();
        if (!$slotStatus['complete']) {
            $message = 'Komposisi slot belum lengkap. ';
            if ($slotStatus['duration_minutes'] !== null) {
                $message .= 'Total persentase ' . $slotStatus['total_percent'] . '% dan total menit ' . $slotStatus['total_minutes'] . ' dari ' . $slotStatus['duration_minutes'] . ' menit. Lengkapi slot terlebih dahulu.';
            } else {
                $message .= 'Total persentase ' . $slotStatus['total_percent'] . '%. Lengkapi slot terlebih dahulu.';
            }
            return redirect()->route('tasks.show', $task)->with('error', $message);
        }

        // Authorization: The user must be the assignee, or an admin
        $isAssignee = $task->assigned_to === $user->id;
        $isAdmin = $user->hasRole(['Super Admin', 'Admin Lokasi']);

        if (!$isAssignee && !$isAdmin) {
            abort(403, 'Anda tidak berhak memperbarui progres tugas ini.');
        }

        $task->load(['slots.attachments', 'assignee']);
        $progressUpdates = $task->progressUpdates()->with(['user', 'approver'])->latest()->get();

        return view('tasks.progress.create', compact('task', 'progressUpdates'));
    }

    public function store(Request $request, Task $task)
    {
        $user = Auth::user();

        if ($task->requires_approval && $task->approval_status !== 'approved') {
            return redirect()->route('tasks.show', $task)->with('error', 'Tugas ini belum disetujui, progres belum dapat diperbarui.');
        }

        if (!$task->slots()->exists()) {
            return redirect()->route('tasks.show', $task)->with('error', 'Tugas belum memiliki slot. Tambahkan slot progres terlebih dahulu.');
        }

        $slotStatus = $task->getSlotCompositionStatus();
        if (!$slotStatus['complete']) {
            $message = 'Komposisi slot belum lengkap. ';
            if ($slotStatus['duration_minutes'] !== null) {
                $message .= 'Total persentase ' . $slotStatus['total_percent'] . '% dan total menit ' . $slotStatus['total_minutes'] . ' dari ' . $slotStatus['duration_minutes'] . ' menit. Lengkapi slot terlebih dahulu.';
            } else {
                $message .= 'Total persentase ' . $slotStatus['total_percent'] . '%. Lengkapi slot terlebih dahulu.';
            }
            return redirect()->route('tasks.show', $task)->with('error', $message);
        }

        // Authorization: The user must be the assignee, or an admin
        $isAssignee = $task->assigned_to === $user->id;
        $isAdmin = $user->hasRole(['Super Admin', 'Admin Lokasi']);

        if (!$isAssignee && !$isAdmin) {
            abort(403, 'Hanya penerima tugas atau admin yang boleh mengirim progres.');
        }

        $data = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'note' => 'nullable|string',
            'link' => 'required|url|max:2000',
        ]);

        // [MODIFIED] Get approval info
        $approvalInfo = $this->determineApprovalInfo($user, $task);

        $progressUpdate = TaskProgressUpdate::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'progress' => $data['progress'],
            'note' => $data['note'] ?? null,
            'photo_path' => null,
            'document_path' => $data['link'],
            'approval_level' => $approvalInfo['level'],
            'requires_approval' => $approvalInfo['requires_approval'],
            'approval_status' => $approvalInfo['status'],
            'approved_by' => $approvalInfo['approver_id'], // Can be pre-filled if no approval needed
            'approved_at' => $approvalInfo['requires_approval'] ? null : now(),
        ]);

        if (!$approvalInfo['requires_approval']) {
            $task->applyProgress($progressUpdate->progress);
        }

        $message = $approvalInfo['requires_approval']
            ? 'Progres terkirim dan menunggu persetujuan.'
            : 'Progres diperbarui.';

        return redirect()->route('tasks.show', $task)->with('success', $message);
    }
    
    // ... (approvals, approve, reject methods remain mostly the same, but might need tweaks later)
    public function approvals(Request $request)
    {
        $user = Auth::user();
        $isManager = \App\Models\Employee::where('master_id', $user->id)->exists();

        if (!$user->hasRole(['Super Admin', 'Admin Lokasi']) && !$isManager) {
            abort(403, 'Anda tidak punya akses ke halaman persetujuan.');
        }

        $search = $request->get('search');
        $locationId = $request->get('location_id');
        $assigneeId = $request->get('assignee_id');

        $taskApprovalQuery = Task::where('requires_approval', true)
            ->where('approval_status', 'pending')
            ->with(['assignee.location', 'assigner']);

        if ($user->hasRole('Super Admin')) {
            // all pending creation requests
        } elseif ($user->hasRole('Admin Lokasi')) {
            $taskApprovalQuery->where('approval_level', 'location_admin')
                ->whereHas('assignee', function ($q) use ($user) {
                    $q->where('location_id', $user->location_id);
                });
        } else {
            $taskApprovalQuery->whereRaw('1 = 0');
        }

        if ($search) {
            $taskApprovalQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('assignee', function ($qq) use ($search) {
                        $qq->where('name', 'like', "%{$search}%");
                    });
            });
        }
        if ($locationId) {
            $taskApprovalQuery->whereHas('assignee', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            });
        }
        if ($assigneeId) {
            $taskApprovalQuery->where('assigned_to', $assigneeId);
        }

        $query = TaskProgressUpdate::where('approval_status', 'pending')->with(['task.assignee.location', 'user']);

        $query->where(function ($q) use ($user, $isManager) {
            // Super Admin sees all 'super_admin' level requests, and also 'location_admin' as a fallback.
            if ($user->hasRole('Super Admin')) {
                $q->whereIn('approval_level', ['super_admin', 'location_admin', 'manager']);
            }
            // Admin Lokasi sees 'location_admin' requests for their location
            elseif ($user->hasRole('Admin Lokasi')) {
                $q->where('approval_level', 'location_admin')
                  ->whereHas('task.assignee', function ($assigneeQuery) use ($user) {
                      $assigneeQuery->where('location_id', $user->location_id);
                  });
            }
            
            // A manager sees requests where they are the designated approver
            if ($isManager) {
                $q->orWhere(function ($managerQuery) use ($user) {
                    $managerQuery->where('approval_level', 'manager')
                                 ->where('approved_by', $user->id);
                });
            }
        });

        $pendingUpdates = $query->orderBy('created_at', 'desc')->paginate(15);

        // Task-level pending (slot-based)
        $taskQuery = Task::with(['assignee.location', 'slots' => function ($q) {
            $q->where('status', 'pending')->whereHas('attachments', function ($aq) {
                $aq->where('type', 'link');
            })->with('attachments');
        }])->whereHas('slots', function ($q) {
            $q->where('status', 'pending')->whereHas('attachments', function ($aq) {
                $aq->where('type', 'link');
            });
        });

        if ($search) {
            $taskQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('assignee', function ($qq) use ($search) {
                        $qq->where('name', 'like', "%{$search}%");
                    });
            });
        }
        if ($locationId) {
            $taskQuery->whereHas('assignee', function ($q) use ($locationId) {
                $q->where('location_id', $locationId);
            });
        }
        if ($assigneeId) {
            $taskQuery->where('assigned_to', $assigneeId);
        }

        if ($user->hasRole('Super Admin')) {
            // all tasks
        } elseif ($user->hasRole('Admin Lokasi')) {
            $taskQuery->whereHas('assignee', function ($q) use ($user) {
                $q->where('location_id', $user->location_id);
            });
        } elseif ($isManager) {
            $taskQuery->whereHas('assignee', function ($q) use ($user) {
                $q->where('master_id', $user->id);
            });
        } else {
            abort(403);
        }
        $pendingTasks = $taskQuery->orderBy('due_date', 'asc')->get();

        $pendingTaskCreations = $taskApprovalQuery->orderBy('created_at', 'desc')->get();

        $locations = \App\Models\Location::orderBy('name')->get();
        $assignees = $user->hasRole('Super Admin')
            ? \App\Models\User::role(['Karyawan', 'Admin Lokasi'])->orderBy('name')->get()
            : \App\Models\User::where('location_id', $user->location_id)->orderBy('name')->get();

        return view('tasks.progress-approvals', compact('pendingUpdates', 'pendingTasks', 'pendingTaskCreations', 'locations', 'assignees', 'search', 'locationId', 'assigneeId'));
    }

    public function approve(TaskProgressUpdate $progressUpdate)
    {
        $this->authorizeApproval(Auth::user(), $progressUpdate);

        if ($progressUpdate->approval_status !== 'pending') {
            return back()->with('info', 'Progres ini sudah diproses.');
        }

        // [MODIFIED] In case of multi-level, this approval might trigger the next level
        // For now, we assume one level of approval
        $before = $progressUpdate->only(['approval_status', 'approved_by', 'approved_at', 'rejection_reason']);
        $progressUpdate->update([
            'approval_status' => 'approved',
            'approved_by' => Auth::id(), // The current user approves it
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);
        AuditLogger::record('task_progress_approved', $progressUpdate, $before, [
            'approval_status' => $progressUpdate->approval_status,
            'approved_by' => $progressUpdate->approved_by,
            'approved_at' => $progressUpdate->approved_at,
        ], [
            'task_id' => $progressUpdate->task_id,
        ]);

        $progressUpdate->task->applyProgress($progressUpdate->progress);

        return back()->with('success', 'Progres disetujui.');
    }

    public function reject(Request $request, TaskProgressUpdate $progressUpdate)
    {
        $this->authorizeApproval(Auth::user(), $progressUpdate);

        if ($progressUpdate->approval_status !== 'pending') {
            return back()->with('info', 'Progres ini sudah diproses.');
        }

        $request->validate(['reason' => 'required|string|max:1000']);

        $before = $progressUpdate->only(['approval_status', 'approved_by', 'approved_at', 'rejection_reason']);
        $progressUpdate->update([
            'approval_status' => 'rejected',
            'approved_by' => Auth::id(), // The current user rejects it
            'approved_at' => now(),
            'rejection_reason' => $request->reason,
        ]);
        AuditLogger::record('task_progress_rejected', $progressUpdate, $before, [
            'approval_status' => $progressUpdate->approval_status,
            'approved_by' => $progressUpdate->approved_by,
            'approved_at' => $progressUpdate->approved_at,
            'rejection_reason' => $progressUpdate->rejection_reason,
        ], [
            'task_id' => $progressUpdate->task_id,
        ]);

        return back()->with('success', 'Progres ditolak dengan alasan dikirim ke karyawan.');
    }

    private function authorizeApproval(User $user, TaskProgressUpdate $progressUpdate): void
    {
        if ($progressUpdate->approval_status !== 'pending') {
            abort(403, 'Progres ini sudah diproses.');
        }

        $level = $progressUpdate->approval_level;

        // The explicitly assigned approver can always approve.
        if ($progressUpdate->approved_by === $user->id) {
            return;
        }

        // Super Admins can handle any level.
        if ($user->hasRole('Super Admin')) {
            return;
        }
        
        // Admin Lokasi can handle 'location_admin' level for their location.
        if ($user->hasRole('Admin Lokasi') && $level === 'location_admin') {
            if (optional($progressUpdate->task->assignee)->location_id === $user->location_id) {
                return;
            }
        }

        abort(403, 'Anda tidak berhak memproses progres ini.');
    }

    // [REPLACED] Old determineApprovalLevel with new comprehensive logic
    private function determineApprovalInfo(User $updater, Task $task): array
    {
        $taskCreator = $task->assigner;

        // 1. Super Admin updates their own or other's task -> No approval
        if ($updater->hasRole('Super Admin')) {
            return [
                'level' => 'none',
                'requires_approval' => false,
                'status' => 'approved',
                'approver_id' => $updater->id
            ];
        }

        // 2. Admin Lokasi mengirim progres -> auto disetujui (self-approved)
        if ($updater->hasRole('Admin Lokasi')) {
            return [
                'level' => 'none',
                'requires_approval' => false,
                'status' => 'approved',
                'approver_id' => $updater->id,
            ];
        }

        // 3. Karyawan atau role lain:
        //    - Jika ada Admin Lokasi untuk lokasi user, kirim ke Admin Lokasi
        //    - Jika tidak ada Admin Lokasi, kirim ke Super Admin
        $locationAdmin = $updater->location_id
            ? User::role('Admin Lokasi')->where('location_id', $updater->location_id)->first()
            : null;
        if ($locationAdmin) {
            return [
                'level' => 'location_admin',
                'requires_approval' => true,
                'status' => 'pending',
                'approver_id' => $locationAdmin->id,
            ];
        }

        $superAdmin = User::role('Super Admin')->first();
        return [
            'level' => 'super_admin',
            'requires_approval' => true,
            'status' => 'pending',
            'approver_id' => $superAdmin ? $superAdmin->id : null,
        ];
    }

    public function downloadAttachment(TaskProgressUpdate $progressUpdate, string $type)
    {
        $user = Auth::user();
        $task = $progressUpdate->task;

        if (!in_array($type, ['photo', 'document'])) {
            abort(404);
        }

        $canView = $user->hasRole('Super Admin')
            || $task->assigned_to === $user->id
            || ($user->hasRole('Admin Lokasi') && optional($task->assignee)->location_id === $user->location_id);

        if (!$canView) {
            abort(403);
        }

        $path = $type === 'photo' ? $progressUpdate->photo_path : $progressUpdate->document_path;

        if (!$path || !Storage::disk('public')->exists($path)) {
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                return redirect()->away($path);
            }
            abort(404);
        }

        return Storage::disk('public')->download($path);
    }
}
