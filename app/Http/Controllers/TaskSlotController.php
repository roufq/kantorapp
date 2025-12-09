<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskSlot;
use App\Models\TaskSlotAttachment;
use App\Models\TaskSlotHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskSlotController extends Controller
{
    private function ensureCanManageStructure(Task $task): void
    {
        $user = Auth::user();
        if (!$user->hasRole(['Super Admin', 'Admin Lokasi'])) {
            abort(403);
        }
        if ($user->hasRole('Admin Lokasi')) {
            if (optional($task->assignee)->location_id !== $user->location_id) {
                abort(403);
            }
        }
    }

    private function ensureCanApprove(TaskSlot $slot): void
    {
        $user = Auth::user();
        if ($user->hasRole('Super Admin')) {
            return;
        }
        if ($user->hasRole('Admin Lokasi')) {
            if (optional($slot->task->assignee)->location_id !== $user->location_id) {
                abort(403);
            }
            return;
        }
        abort(403);
    }

    private function ensureCanApproveTask(Task $task): void
    {
        $user = Auth::user();
        if ($user->hasRole('Super Admin')) {
            return;
        }
        if ($user->hasRole('Admin Lokasi') && optional($task->assignee)->location_id === $user->location_id) {
            return;
        }
        abort(403);
    }

    private function ensureCanSubmit(TaskSlot $slot): void
    {
        $user = Auth::user();
        if ($user->id === optional($slot->task)->assigned_to) {
            return;
        }
        if ($user->hasRole(['Super Admin', 'Admin Lokasi'])) {
            return;
        }
        abort(403);
    }

    public function store(Request $request, Task $task)
    {
        $this->ensureCanManageStructure($task);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|integer|min:1|max:100',
            'minutes' => 'required|integer|min:1',
            'order' => 'nullable|integer|min:0|max:255',
        ]);

        $totalPercent = $task->slots()->sum('percentage') + $data['percentage'];
        if ($totalPercent > 100) {
            return back()->withErrors(['percentage' => 'Total persentase slot melebihi 100% (saat ini: ' . $totalPercent . '%).'])->withInput();
        }

        $totalMinutes = $task->slots()->sum('minutes') + $data['minutes'];
        if ($task->duration_minutes && $totalMinutes > $task->duration_minutes) {
            return back()->withErrors(['minutes' => 'Total menit slot melebihi durasi task.'])->withInput();
        }

        $slot = $task->slots()->create([
            'name' => $data['name'],
            'percentage' => $data['percentage'],
            'minutes' => $data['minutes'],
            'order' => $data['order'] ?? 0,
            'created_by' => Auth::id(),
        ]);

        TaskSlotHistory::create([
            'task_slot_id' => $slot->id,
            'action' => 'created',
            'data_after' => $slot->toArray(),
            'actor_id' => Auth::id(),
        ]);

        return back()->with('success', 'Slot ditambahkan.');
    }

    public function update(Request $request, Task $task, TaskSlot $slot)
    {
        $this->ensureCanManageStructure($task);
        if ($slot->task_id !== $task->id) {
            abort(404);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|integer|min:1|max:100',
            'minutes' => 'required|integer|min:1',
            'order' => 'nullable|integer|min:0|max:255',
        ]);

        $totalPercent = $task->slots()->where('id', '!=', $slot->id)->sum('percentage') + $data['percentage'];
        if ($totalPercent > 100) {
            return back()->withErrors(['percentage' => 'Total persentase slot melebihi 100% (saat ini: ' . $totalPercent . '%).'])->withInput();
        }

        $totalMinutes = $task->slots()->where('id', '!=', $slot->id)->sum('minutes') + $data['minutes'];
        if ($task->duration_minutes && $totalMinutes > $task->duration_minutes) {
            return back()->withErrors(['minutes' => 'Total menit slot melebihi durasi task.'])->withInput();
        }

        $before = $slot->toArray();
        $slot->update([
            'name' => $data['name'],
            'percentage' => $data['percentage'],
            'minutes' => $data['minutes'],
            'order' => $data['order'] ?? 0,
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
        ]);

        TaskSlotHistory::create([
            'task_slot_id' => $slot->id,
            'action' => 'updated',
            'data_before' => $before,
            'data_after' => $slot->toArray(),
            'actor_id' => Auth::id(),
        ]);

        $task->recalcProgressFromSlots();

        return back()->with('success', 'Slot diperbarui.');
    }

    public function destroy(Task $task, TaskSlot $slot)
    {
        $this->ensureCanManageStructure($task);
        if ($slot->task_id !== $task->id) {
            abort(404);
        }

        $before = $slot->toArray();
        $slot->delete();

        TaskSlotHistory::create([
            'task_slot_id' => $slot->id,
            'action' => 'deleted',
            'data_before' => $before,
            'actor_id' => Auth::id(),
        ]);

        $task->recalcProgressFromSlots();

        return back()->with('success', 'Slot dihapus.');
    }

    public function submit(Request $request, TaskSlot $slot)
    {
        $this->ensureCanSubmit($slot);

        $data = $request->validate([
            'note' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
            'document' => 'nullable|file|mimes:pdf,doc,docx,txt,xls,xlsx|max:8192',
            'link' => 'nullable|url|max:2000',
        ]);

        if (!$request->hasFile('photo') && !$request->hasFile('document') && empty($data['link'])) {
            return back()->withErrors(['photo' => 'Minimal satu bukti (foto/dokumen/link) wajib diisi.'])->withInput();
        }

        $before = $slot->toArray();
        $attachments = [];

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('task-slots/photos', 'public');
            $attachments[] = ['type' => 'photo', 'path_or_url' => $path];
        }
        if ($request->hasFile('document')) {
            $path = $request->file('document')->store('task-slots/documents', 'public');
            $attachments[] = ['type' => 'document', 'path_or_url' => $path];
        }
        if (!empty($data['link'])) {
            $attachments[] = ['type' => 'link', 'path_or_url' => $data['link']];
        }

        foreach ($attachments as $att) {
            TaskSlotAttachment::create([
                'task_slot_id' => $slot->id,
                'type' => $att['type'],
                'path_or_url' => $att['path_or_url'],
                'uploaded_by' => Auth::id(),
            ]);
        }

        $slot->update([
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
            'rejection_reason' => null,
        ]);

        TaskSlotHistory::create([
            'task_slot_id' => $slot->id,
            'action' => 'updated',
            'data_before' => $before,
            'data_after' => $slot->toArray(),
            'actor_id' => Auth::id(),
        ]);

        return back()->with('success', 'Bukti progres slot dikirim, menunggu approval.');
    }

    public function approve(TaskSlot $slot)
    {
        $this->ensureCanApprove($slot);

        $before = $slot->toArray();
        $slot->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);

        TaskSlotHistory::create([
            'task_slot_id' => $slot->id,
            'action' => 'approved',
            'data_before' => $before,
            'data_after' => $slot->toArray(),
            'actor_id' => Auth::id(),
        ]);

        $slot->task->recalcProgressFromSlots();

        return back()->with('success', 'Slot disetujui.');
    }

    public function approveTask(Task $task)
    {
        $this->ensureCanApproveTask($task);

        $pendingSlots = $task->slots()->where('status', 'pending')->get();
        if ($pendingSlots->isEmpty()) {
            return back()->with('info', 'Tidak ada slot pending untuk tugas ini.');
        }

        DB::transaction(function () use ($pendingSlots, $task) {
            foreach ($pendingSlots as $slot) {
                $before = $slot->toArray();
                $slot->update([
                    'status' => 'approved',
                    'approved_by' => Auth::id(),
                    'approved_at' => now(),
                    'rejection_reason' => null,
                ]);

                TaskSlotHistory::create([
                    'task_slot_id' => $slot->id,
                    'action' => 'approved',
                    'data_before' => $before,
                    'data_after' => $slot->toArray(),
                    'actor_id' => Auth::id(),
                ]);
            }

            $task->recalcProgressFromSlots();
        });

        return back()->with('success', 'Semua slot pending pada tugas ini telah disetujui.');
    }

    public function reject(Request $request, TaskSlot $slot)
    {
        $this->ensureCanApprove($slot);

        $data = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

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

        $slot->task->recalcProgressFromSlots();

        return back()->with('success', 'Slot ditolak.');
    }

    public function rejectTask(Request $request, Task $task)
    {
        $this->ensureCanApproveTask($task);

        $data = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $pendingSlots = $task->slots()->where('status', 'pending')->get();
        if ($pendingSlots->isEmpty()) {
            return back()->with('info', 'Tidak ada slot pending untuk tugas ini.');
        }

        DB::transaction(function () use ($pendingSlots, $task, $data) {
            foreach ($pendingSlots as $slot) {
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

            $task->recalcProgressFromSlots();
        });

        return back()->with('success', 'Semua slot pending pada tugas ini telah ditolak.');
    }
}
