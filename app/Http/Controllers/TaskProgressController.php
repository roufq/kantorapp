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
    public function store(Request $request, Task $task)
    {
        $user = Auth::user();

        if ($task->assigned_to !== $user->id) {
            abort(403, 'Hanya penerima tugas yang boleh mengirim progres.');
        }

        $data = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
            'note' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'document' => 'nullable|file|mimes:pdf,doc,docx,txt,xls,xlsx|max:8192',
        ]);

        if (!$request->hasFile('photo') && !$request->hasFile('document')) {
            return back()->withErrors(['photo' => 'Lampirkan foto atau dokumen untuk memvalidasi progres.'])->withInput();
        }

        $approvalLevel = $this->determineApprovalLevel($user, $task);
        $requiresApproval = $approvalLevel !== 'none';
        $approvalStatus = $requiresApproval ? 'pending' : 'approved';

        $photoPath = $request->hasFile('photo') ? $request->file('photo')->store('task-progress/photos', 'public') : null;
        $documentPath = $request->hasFile('document') ? $request->file('document')->store('task-progress/documents', 'public') : null;

        $progressUpdate = TaskProgressUpdate::create([
            'task_id' => $task->id,
            'user_id' => $user->id,
            'progress' => $data['progress'],
            'note' => $data['note'] ?? null,
            'photo_path' => $photoPath,
            'document_path' => $documentPath,
            'approval_level' => $approvalLevel,
            'requires_approval' => $requiresApproval,
            'approval_status' => $approvalStatus,
            'approved_by' => $requiresApproval ? null : $user->id,
            'approved_at' => $requiresApproval ? null : now(),
        ]);

        if (!$requiresApproval) {
            $task->applyProgress($progressUpdate->progress);
        }

        $message = $requiresApproval
            ? 'Progres terkirim dan menunggu persetujuan.'
            : 'Progres diperbarui.';

        return redirect()->route('tasks.show', $task)->with('success', $message);
    }

    public function approvals(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('Super Admin') && !$user->hasRole('Admin Lokasi')) {
            abort(403);
        }

        $query = TaskProgressUpdate::where('approval_status', 'pending')
            ->with(['task.assignee', 'user']);

        if ($user->hasRole('Admin Lokasi') && !$user->hasRole('Super Admin')) {
            $query->where('approval_level', 'location_admin')
                ->whereHas('task.assignee', function ($q) use ($user) {
                    $q->where('location_id', $user->location_id);
                });
        }

        $pendingUpdates = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('tasks.progress-approvals', compact('pendingUpdates'));
    }

    public function approve(TaskProgressUpdate $progressUpdate)
    {
        $user = Auth::user();
        $this->authorizeApproval($user, $progressUpdate);

        if ($progressUpdate->approval_status !== 'pending') {
            return back()->with('info', 'Progres ini sudah diproses.');
        }

        $progressUpdate->update([
            'approval_status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        $progressUpdate->task->applyProgress($progressUpdate->progress);

        return back()->with('success', 'Progres disetujui.');
    }

    public function reject(Request $request, TaskProgressUpdate $progressUpdate)
    {
        $user = Auth::user();
        $this->authorizeApproval($user, $progressUpdate);

        if ($progressUpdate->approval_status !== 'pending') {
            return back()->with('info', 'Progres ini sudah diproses.');
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $progressUpdate->update([
            'approval_status' => 'rejected',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'rejection_reason' => $request->reason,
        ]);

        return back()->with('success', 'Progres ditolak dengan alasan dikirim ke karyawan.');
    }

    private function authorizeApproval(User $user, TaskProgressUpdate $progressUpdate): void
    {
        if ($user->hasRole('Super Admin')) {
            return;
        }

        if ($progressUpdate->approval_level === 'location_admin'
            && $user->hasRole('Admin Lokasi')
            && optional($progressUpdate->task->assignee)->location_id === $user->location_id) {
            return;
        }

        abort(403, 'Anda tidak berhak memproses progres ini.');
    }

    private function determineApprovalLevel(User $user, Task $task): string
    {
        if ($user->hasRole('Super Admin')) {
            return 'none';
        }

        if ($user->hasRole('Admin Lokasi')) {
            return 'super_admin';
        }

        $assigneeLocation = optional($task->assignee)->location_id;
        $hasLocationAdmin = $assigneeLocation
            ? User::role('Admin Lokasi')->where('location_id', $assigneeLocation)->exists()
            : false;

        return $hasLocationAdmin ? 'location_admin' : 'super_admin';
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
