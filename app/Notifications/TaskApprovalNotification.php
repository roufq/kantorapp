<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskApprovalNotification extends Notification
{
    use Queueable;

    public function __construct(
        private Task $task,
        private string $status // approved | rejected
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'task_approval',
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'status' => $this->status,
            'message' => $this->status === 'approved'
                ? 'Tugas Anda disetujui'
                : 'Tugas Anda ditolak',
            'approval_note' => $this->task->approval_note,
        ];
    }
}
