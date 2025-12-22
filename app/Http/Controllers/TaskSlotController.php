<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskSlot;
use App\Models\TaskSlotAttachment;
use App\Models\TaskSlotHistory;
use App\Models\LocationWorkTarget;
use App\Models\EmployeeWorkRecap;
use App\Models\User;
use App\Notifications\TaskSlotSubmittedNotification;
use App\Notifications\TaskSlotApprovalNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TaskSlotController extends Controller
{
    private function ensureCanManageStructure(Task $task): void
    {
        $user = Auth::user();
        if ($user->hasRole('Super Admin')) {
            return;
        }
        if (!$user->hasRole('Admin Lokasi')) {
            abort(403);
        }
        if ($task->requires_approval && $task->approval_status !== 'approved') {
            abort(403, 'Struktur tugas belum bisa diubah karena tugas menunggu persetujuan.');
        }
        if (optional($task->assignee)->location_id !== $user->location_id) {
            abort(403);
        }
    }

    private function ensureCanApprove(TaskSlot $slot): void
    {
        $user = Auth::user();
        if ($user->hasRole('Super Admin')) {
            return;
        }
        if ($slot->task && $slot->task->requires_approval && $slot->task->approval_status !== 'approved') {
            abort(403, 'Tugas ini belum disetujui.');
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
        if ($task->requires_approval && $task->approval_status !== 'approved') {
            abort(403, 'Tugas ini belum disetujui.');
        }
        if ($user->hasRole('Admin Lokasi') && optional($task->assignee)->location_id === $user->location_id) {
            return;
        }
        abort(403);
    }

    private function ensureCanSubmit(TaskSlot $slot): void
    {
        $user = Auth::user();
        if ($slot->task && $slot->task->requires_approval && $slot->task->approval_status !== 'approved') {
            abort(403, 'Tugas ini belum disetujui.');
        }
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
            'percentage' => 'required|numeric|min:0.01|max:100',
            'minutes' => 'required|integer|min:1',
            'order' => 'nullable|integer|min:0|max:255',
        ]);

        $totalPercent = (float) $task->slots()->sum('percentage') + (float) $data['percentage'];
        if ($totalPercent > 100.01) {
            return back()->withErrors(['percentage' => 'Total persentase slot melebihi 100% (saat ini: ' . $totalPercent . '%).'])->withInput();
        }

        $totalMinutes = $task->slots()->sum('minutes') + $data['minutes'];
        if ($task->duration_minutes && $totalMinutes > $task->duration_minutes) {
            return back()->withErrors(['minutes' => 'Total menit slot melebihi durasi task.'])->withInput();
        }

        $slot = $task->slots()->create([
            'name' => $data['name'],
            'percentage' => round((float) $data['percentage'], 2),
            'minutes' => $data['minutes'],
            'order' => $data['order'] ?? 0,
            'created_by' => Auth::id(),
            'status' => 'pending',
        ]);

        TaskSlotHistory::create([
            'task_slot_id' => $slot->id,
            'action' => 'created',
            'data_after' => $slot->toArray(),
            'actor_id' => Auth::id(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            $slotStatus = $task->getSlotCompositionStatus();
            $missingPercent = max(0, round(100 - $slotStatus['total_percent'], 2));
            $missingMinutes = $slotStatus['duration_minutes'] !== null
                ? max(0, $slotStatus['duration_minutes'] - $slotStatus['total_minutes'])
                : null;
            $slotAlertMessage = 'Komposisi slot belum lengkap. ';
            if ($slotStatus['duration_minutes'] !== null) {
                $slotAlertMessage .= 'Kurang ' . $missingPercent . '% dan ' . $missingMinutes . ' menit. Lengkapi slot terlebih dahulu.';
            } else {
                $slotAlertMessage .= 'Kurang ' . $missingPercent . '%. Lengkapi slot terlebih dahulu.';
            }

            return response()->json([
                'slot_html' => view('tasks.partials.slot-card', [
                    'task' => $task,
                    'slot' => $slot,
                    'slotIncomplete' => !$slotStatus['complete'],
                    'slotAlertMessage' => $slotAlertMessage,
                ])->render(),
                'slot_status' => [
                    'complete' => $slotStatus['complete'],
                    'total_percent' => $slotStatus['total_percent'],
                    'total_minutes' => $slotStatus['total_minutes'],
                    'duration_minutes' => $slotStatus['duration_minutes'],
                    'missing_percent' => $missingPercent,
                    'missing_minutes' => $missingMinutes,
                    'message' => $slotAlertMessage,
                ],
            ]);
        }

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
            'percentage' => 'required|numeric|min:0.01|max:100',
            'minutes' => 'required|integer|min:1',
            'order' => 'nullable|integer|min:0|max:255',
        ]);

        $totalPercent = (float) $task->slots()->where('id', '!=', $slot->id)->sum('percentage') + (float) $data['percentage'];
        if ($totalPercent > 100.01) {
            return back()->withErrors(['percentage' => 'Total persentase slot melebihi 100% (saat ini: ' . $totalPercent . '%).'])->withInput();
        }

        $totalMinutes = $task->slots()->where('id', '!=', $slot->id)->sum('minutes') + $data['minutes'];
        if ($task->duration_minutes && $totalMinutes > $task->duration_minutes) {
            return back()->withErrors(['minutes' => 'Total menit slot melebihi durasi task.'])->withInput();
        }

        $before = $slot->toArray();
        $slot->update([
            'name' => $data['name'],
            'percentage' => round((float) $data['percentage'], 2),
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
        TaskSlotHistory::create([
            'task_slot_id' => $slot->id,
            'action' => 'deleted',
            'data_before' => $before,
            'actor_id' => Auth::id(),
        ]);
        $slot->delete();

        $task->recalcProgressFromSlots();

        return back()->with('success', 'Slot dihapus.');
    }

    public function submit(Request $request, TaskSlot $slot)
    {
        $this->ensureCanSubmit($slot);

        $task = $slot->task;
        if ($task) {
            $slotStatus = $task->getSlotCompositionStatus();
            if (!$slotStatus['complete']) {
                $message = 'Komposisi slot belum lengkap. ';
                if ($slotStatus['duration_minutes'] !== null) {
                    $message .= 'Total persentase ' . $slotStatus['total_percent'] . '% dan total menit ' . $slotStatus['total_minutes'] . ' dari ' . $slotStatus['duration_minutes'] . ' menit. Lengkapi slot terlebih dahulu.';
                } else {
                    $message .= 'Total persentase ' . $slotStatus['total_percent'] . '%. Lengkapi slot terlebih dahulu.';
                }
                return back()->withErrors(['link' => $message]);
            }
        }

        // Hanya boleh submit pertama kali (pending tanpa lampiran) atau setelah reject
        $hasAttachments = $slot->attachments()->exists();
        if ($slot->status === 'approved') {
            return back()->withErrors(['link' => 'Slot sudah disetujui. Tidak dapat menambah bukti lagi.']);
        }
        if (in_array($slot->status, ['pending']) && $hasAttachments) {
            return back()->withErrors(['link' => 'Slot sudah memiliki bukti dan menunggu approval. Tunggu hasil atau ajukan ulang setelah reject.']);
        }

        $data = $request->validate([
            'note' => 'nullable|string',
            'link' => 'required|url|max:2000',
        ]);

        $before = $slot->toArray();
        TaskSlotAttachment::create([
            'task_slot_id' => $slot->id,
            'type' => 'link',
            'path_or_url' => $data['link'],
            'uploaded_by' => Auth::id(),
        ]);

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

        // Notifikasi: kiriman karyawan -> Admin Lokasi & Super Admin; kiriman Admin Lokasi -> Super Admin
        $submitter = Auth::user();
        $task = $slot->task;
        $submittedByRole = $submitter->hasRole('Admin Lokasi') ? 'admin_lokasi' : ($submitter->hasRole('Super Admin') ? 'super_admin' : 'karyawan');
        $notifyUsers = collect();

        if ($submitter->hasRole('Admin Lokasi')) {
            $notifyUsers = $notifyUsers->merge(User::role('Super Admin')->get());
        } elseif ($submitter->hasRole('Super Admin')) {
            // do nothing (already highest)
        } else {
            // karyawan
            if (optional($task->assignee)->location_id) {
                $notifyUsers = $notifyUsers->merge(User::role('Admin Lokasi')->where('location_id', $task->assignee->location_id)->get());
            }
            $notifyUsers = $notifyUsers->merge(User::role('Super Admin')->get());
        }

        $notifyUsers = $notifyUsers->unique('id')->reject(fn($u) => $u->id === $submitter->id);
        foreach ($notifyUsers as $recipient) {
            $recipient->notify(new TaskSlotSubmittedNotification($task, $slot, $submittedByRole));
        }

        return back()->with('success', 'Bukti progres slot dikirim, menunggu approval.');
    }

    public function approve(TaskSlot $slot)
    {
        $this->ensureCanApprove($slot);

        $task = $slot->task;
        $minutes = (int) ($slot->minutes ?? 0);

        try {
            DB::transaction(function () use ($slot, $task, $minutes) {
                $this->applyWorkTargetConsumption($task, $minutes);

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
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        if ($slot->task && $slot->task->assignee) {
            $slot->task->assignee->notify(new TaskSlotApprovalNotification($slot, 'approved'));
        }

        return back()->with('success', 'Slot disetujui.');
    }

    public function approveTask(Task $task)
    {
        $this->ensureCanApproveTask($task);

        $pendingSlots = $task->slots()->where('status', 'pending')->get();
        if ($pendingSlots->isEmpty()) {
            return back()->with('info', 'Tidak ada slot pending untuk tugas ini.');
        }

        $totalMinutes = (int) $pendingSlots->sum('minutes');

        try {
            DB::transaction(function () use ($pendingSlots, $task, $totalMinutes) {
                $this->applyWorkTargetConsumption($task, $totalMinutes);

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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        if ($slot->task && $slot->task->assignee) {
            $slot->task->assignee->notify(new TaskSlotApprovalNotification($slot, 'approved'));
        }

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

        if ($slot->task && $slot->task->assignee) {
            $slot->task->assignee->notify(new TaskSlotApprovalNotification($slot, 'rejected', $data['reason']));
        }

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

    /**
     * Kurangi target jam kerja (menit) karyawan saat slot disetujui.
     * Jika tidak ada target, dilewati. Jika sisa tidak cukup, lempar ValidationException.
     */
    private function applyWorkTargetConsumption(Task $task, int $minutesToConsume): void
    {
        if ($minutesToConsume <= 0) {
            return;
        }

        $assignee = $task->assignee;
        if (!$assignee) {
            return;
        }

        $employeeId = $assignee->employee_id ?? $assignee->karyawan_id;
        $locationId = $assignee->location_id;
        if (!$employeeId || !$locationId) {
            return;
        }

        $year = now()->year;
        $month = now()->month;

        $target = LocationWorkTarget::where('location_id', $locationId)
            ->where('employee_id', $employeeId)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if (!$target) {
            return;
        }

        $recap = EmployeeWorkRecap::firstOrCreate(
            [
                'employee_id' => $employeeId,
                'location_id' => $locationId,
                'year' => $year,
                'month' => $month,
            ],
            [
                'slot_minutes_approved' => 0,
                'attendance_minutes' => 0,
                'total_minutes' => 0,
            ]
        );

        $currentApproved = (int) $recap->slot_minutes_approved;
        $remaining = (int) $target->target_minutes - $currentApproved;

        if ($remaining < $minutesToConsume) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'slots' => 'Sisa jatah jam kerja bulan ini tinggal ' . $remaining . ' menit, tetapi slot memerlukan ' . $minutesToConsume . ' menit. Approval dibatalkan.',
            ]);
        }

        $recap->update([
            'slot_minutes_approved' => $currentApproved + $minutesToConsume,
            'total_minutes' => $currentApproved + $minutesToConsume + (int) $recap->attendance_minutes,
        ]);
    }
}
