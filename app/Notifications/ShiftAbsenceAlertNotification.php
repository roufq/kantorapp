<?php

namespace App\Notifications;

use App\Models\ShiftAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShiftAbsenceAlertNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected ShiftAssignment $assignment, protected string $expectedEnd)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $shiftName = optional($this->assignment->shift)->name ?? 'Shift';
        $locationName = optional($this->assignment->location)->name ?? 'Lokasi';

        return (new MailMessage)
            ->subject("Alert: Tidak ada check-in untuk {$shiftName}")
            ->line("Penugasan {$shiftName} di {$locationName} seharusnya selesai sekitar {$this->expectedEnd}, tetapi belum ada kehadiran tercatat.")
            ->line("Tanggal: {$this->assignment->date->format('Y-m-d')}")
            ->line("User: {$notifiable->name}")
            ->line('Silakan lakukan pengecekan atau update status assignment.');
    }
}
