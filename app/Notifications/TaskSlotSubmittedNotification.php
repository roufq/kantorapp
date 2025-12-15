<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\TaskSlot;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskSlotSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private Task $task,
        private TaskSlot $slot,
        private string $submittedByRole // karyawan | admin_lokasi | super_admin
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'task_slot_submitted',
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'slot_id' => $this->slot->id,
            'slot_name' => $this->slot->name,
            'submitted_by_role' => $this->submittedByRole,
            'message' => "Bukti slot '{$this->slot->name}' dikirim ({$this->submittedByRole})",
        ];
    }
}
