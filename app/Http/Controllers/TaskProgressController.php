<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskProgressUpdate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskProgressController extends Controller
{
    // [NEW] Method to show the progress update form
    public function create(Request $request, Task $task)
    {
        $user = Auth::user();

        // Authorization: The user must be the assignee, or an admin
        $isAssignee = $task->assigned_to === $user->id;
        $isAdmin = $user->hasRole(['Super Admin', 'Admin Lokasi']);

        if (!$isAssignee && !$isAdmin) {
            abort(403, 'Anda tidak berhak memperbarui progres tugas ini.');
        }

        $progressUpdates = $task->progressUpdates()->with(['user', 'approver'])->latest()->get();

        return view('tasks.progress.create', compact('task', 'progressUpdates'));
    }

    public function store(Request $request, Task $task)
    {
        $user = Auth::user();

        // Authorization: The user must be the assignee, or an admin
        $isAssignee = $task->assigned_to === $user->id;
        $isAdmin = $user->hasRole(['Super Admin', 'Admin Lokasi']);

        if (!$isAssignee && !$isAdmin) {
            abort(403, 'Hanya penerima tugas atau admin yang boleh mengirim progres.');
        }

        $data = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'note' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'document' => 'nullable|file|mimes:pdf,doc,docx,txt,xls,xlsx|max:8192',
        ]);
        
        // [MODIFIED] Simplified validation for file upload
        if ($request->hasFile('photo') === false && $request->hasFile('document') === false && empty($data['note'])) {
            return back()->withErrors(['photo' => 'Anda harus memberikan catatan, atau melampirkan foto/dokumen.'])->withInput();
        }

        // [MODIFIED] Get approval info
        $approvalInfo = $this->determineApprovalInfo($user, $task);

        $photoPath = $request->hasFile('photo') ? $request->file('photo')->store('task-progress/photos', 'public') : null;
        $documentPath = $request->hasFile('document') ? $request->file('document')->store('task-progress/documents', 'public') : null;

        $progressUpdate = TaskProgressUpdate::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'progress' => $data['progress'],
            'note' => $data['note'] ?? null,
            'photo_path' => $photoPath,
            'document_path' => $documentPath,
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

        $query = TaskProgressUpdate::where('approval_status', 'pending')->with(['task.assignee.location', 'user']);

        $query->where(function ($q) use ($user, $isManager) {
            // Super Admin sees all 'super_admin' level requests, and also 'location_admin' as a fallback.
            if ($user->hasRole('Super Admin')) {
                $q->whereIn('approval_level', ['super_admin', 'location_admin']);
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

        return view('tasks.progress-approvals', compact('pendingUpdates'));
    }

    public function approve(TaskProgressUpdate $progressUpdate)
    {
        $this->authorizeApproval(Auth::user(), $progressUpdate);

        if ($progressUpdate->approval_status !== 'pending') {
            return back()->with('info', 'Progres ini sudah diproses.');
        }

        // [MODIFIED] In case of multi-level, this approval might trigger the next level
        // For now, we assume one level of approval
        $progressUpdate->update([
            'approval_status' => 'approved',
            'approved_by' => Auth::id(), // The current user approves it
            'approved_at' => now(),
            'rejection_reason' => null,
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

        $progressUpdate->update([
            'approval_status' => 'rejected',
            'approved_by' => Auth::id(), // The current user rejects it
            'approved_at' => now(),
            'rejection_reason' => $request->reason,
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

        // Super Admins can handle 'super_admin' and 'location_admin' levels.
        if ($user->hasRole('Super Admin') && in_array($level, ['super_admin', 'location_admin'])) {
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

        // 2. Task was created by a Super Admin -> Approval goes to that Super Admin
        if ($taskCreator->hasRole('Super Admin')) {
            return [
                'level' => 'super_admin',
                'requires_approval' => true,
                'status' => 'pending',
                'approver_id' => $taskCreator->id
            ];
        }
        
        // 3. Task was created by an Admin Lokasi -> Approval goes to a Super Admin
        if ($taskCreator->hasRole('Admin Lokasi')) {
            $superAdmin = User::role('Super Admin')->first();
            return [
                'level' => 'super_admin',
                'requires_approval' => true,
                'status' => 'pending',
                'approver_id' => $superAdmin ? $superAdmin->id : null // Failsafe
            ];
        }

        // 4. Default case (task created by Karyawan or other)
        // Find the updater's direct manager (atasan) from the Employee model
        $manager = optional($updater->employee)->master; // master is a User object

        if ($manager) {
            return [
                'level' => 'manager', // New level for direct manager
                'requires_approval' => true,
                'status' => 'pending',
                'approver_id' => $manager->id
            ];
        } else {
            // 5. Failsafe: if no manager, send to Super Admin
            $superAdmin = User::role('Super Admin')->first();
            return [
                'level' => 'super_admin',
                'requires_approval' => true,
                'status' => 'pending',
                'approver_id' => $superAdmin ? $superAdmin->id : null
            ];
        }
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
            abort(404);
        }

        return Storage::disk('public')->download($path);
    }
}