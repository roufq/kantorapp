<?php

namespace App\Notifications;

use App\Models\TaskSlot;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskSlotApprovalNotification extends Notification
{
    use Queueable;

    public function __construct(
        private TaskSlot $slot,
        private string $status, // approved | rejected
        private ?string $reason = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'task_slot_approval',
            'task_id' => $this->slot->task_id,
            'task_title' => optional($this->slot->task)->title,
            'slot_id' => $this->slot->id,
            'slot_name' => $this->slot->name,
            'status' => $this->status,
            'reason' => $this->reason,
        ];
    }
}
