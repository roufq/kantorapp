<?php

namespace App\Notifications;

use App\Models\ShiftAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShiftReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected ShiftAssignment $assignment, protected string $startTime)
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
            ->subject("Pengingat Shift: {$shiftName} ({$this->startTime})")
            ->line("Hai {$notifiable->name}, kamu punya penugasan shift {$shiftName}.")
            ->line("Lokasi: {$locationName}")
            ->line("Mulai: {$this->startTime}")
            ->line("Catatan: " . ($this->assignment->notes ?: '-'))
            ->line('Silakan siapkan kehadiran tepat waktu.');
    }
}
