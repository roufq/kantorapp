<?php

namespace App\Console\Commands;

use App\Models\EmployeeLeave;
use App\Models\LocationChangeRequest;
use App\Models\Message;
use App\Models\Overtime;
use App\Models\Task;
use App\Models\TaskProgressUpdate;
use App\Models\TaskSlot;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyPendingApprovals extends Command
{
    protected $signature = 'notify:pending-approvals';
    protected $description = 'Kirim notifikasi in-app untuk approval pending.';

    public function handle(): int
    {
        $senderId = $this->resolveSenderId();
        if (!$senderId) {
            $this->error('Tidak ada user pengirim sistem.');
            return self::FAILURE;
        }

        $today = Carbon::today();
        $employeeCounts = [];
        $adminCounts = [];
        $adminUsers = User::role('Location Admin')->with('location')->get();
        $adminsByLocation = $adminUsers->groupBy('location_id');
        $adminsById = $adminUsers->keyBy('id');

        $overtimes = Overtime::with('user')->where('status', 'pending')->get();
        foreach ($overtimes as $overtime) {
            $user = $overtime->user;
            if (!$user) {
                continue;
            }
            $this->addCount($employeeCounts, $user->id, 'lembur');
            foreach ($adminsByLocation[$user->location_id] ?? [] as $admin) {
                $this->addCount($adminCounts, $admin->id, 'lembur', $user->name ?? null);
            }
        }

        $leaves = EmployeeLeave::with('user')->where('status', 'pending')->get();
        foreach ($leaves as $leave) {
            $user = $leave->user;
            if (!$user) {
                continue;
            }
            $this->addCount($employeeCounts, $user->id, 'cuti');
            foreach ($adminsByLocation[$leave->location_id] ?? [] as $admin) {
                $this->addCount($adminCounts, $admin->id, 'cuti', $user->name ?? null);
            }
        }

        $tasks = Task::with('assignee')
            ->where('requires_approval', true)
            ->where('approval_status', 'pending')
            ->get();
        foreach ($tasks as $task) {
            $assignee = $task->assignee;
            if (!$assignee) {
                continue;
            }
            $this->addCount($employeeCounts, $assignee->id, 'tugas');
            foreach ($adminsByLocation[$assignee->location_id] ?? [] as $admin) {
                $this->addCount($adminCounts, $admin->id, 'tugas', $assignee->name ?? null);
            }
        }

        $progressUpdates = TaskProgressUpdate::with(['task.assignee', 'user'])
            ->where('approval_status', 'pending')
            ->get();
        foreach ($progressUpdates as $update) {
            $assignee = $update->task?->assignee;
            $submitter = $update->user;
            if ($submitter) {
                $this->addCount($employeeCounts, $submitter->id, 'progres');
            }
            if ($assignee) {
                foreach ($adminsByLocation[$assignee->location_id] ?? [] as $admin) {
                    $this->addCount($adminCounts, $admin->id, 'progres', $assignee->name ?? null);
                }
            }
        }

        $slots = TaskSlot::with('task.assignee')
            ->where('status', 'pending')
            ->get();
        foreach ($slots as $slot) {
            $assignee = $slot->task?->assignee;
            if ($assignee) {
                $this->addCount($employeeCounts, $assignee->id, 'slot');
                foreach ($adminsByLocation[$assignee->location_id] ?? [] as $admin) {
                    $this->addCount($adminCounts, $admin->id, 'slot', $assignee->name ?? null);
                }
            }
        }

        $changes = LocationChangeRequest::with('user')
            ->where('status', 'pending')
            ->get();
        foreach ($changes as $change) {
            $user = $change->user;
            if ($user) {
                $this->addCount($employeeCounts, $user->id, 'mutasi');
            }
            foreach ($adminsByLocation[$change->original_location_id] ?? [] as $admin) {
                $this->addCount($adminCounts, $admin->id, 'mutasi', $user?->name ?? null);
            }
        }

        $sent = 0;
        foreach ($employeeCounts as $userId => $counts) {
            $recipient = User::with('location')->find($userId);
            if (!$recipient || !$this->shouldSendForLocation($recipient->location, 'notify_pending_approvals_time')) {
                continue;
            }
            $message = $this->buildPendingMessage($counts, false);
            if (!$message) {
                continue;
            }
            if ($this->sendOncePerDay($senderId, $userId, $message, $today)) {
                $sent++;
            }
        }

        foreach ($adminCounts as $adminId => $counts) {
            $admin = $adminsById[$adminId] ?? null;
            if (!$admin || !$this->shouldSendForLocation($admin->location, 'notify_pending_approvals_time')) {
                continue;
            }
            $message = $this->buildPendingMessage($counts, true, $adminsById[$adminId] ?? null);
            if (!$message) {
                continue;
            }
            if ($this->sendOncePerDay($senderId, $adminId, $message, $today)) {
                $sent++;
            }
        }

        $this->info('Pending approval notifications sent: ' . $sent);
        return self::SUCCESS;
    }

    private function addCount(array &$map, int $userId, string $key): void
    {
        $args = func_get_args();
        $name = $args[3] ?? null;

        $map[$userId] = $map[$userId] ?? [];
        $map[$userId][$key] = $map[$userId][$key] ?? ['count' => 0, 'names' => []];
        $map[$userId][$key]['count'] = ($map[$userId][$key]['count'] ?? 0) + 1;
        if ($name) {
            $map[$userId][$key]['names'][] = $name;
        }
    }

    private function buildPendingMessage(array $counts, bool $isAdmin, ?User $admin = null): ?string
    {
        $labels = [
            'lembur' => 'lembur',
            'cuti' => 'cuti',
            'tugas' => 'tugas',
            'progres' => 'progres',
            'slot' => 'slot',
            'mutasi' => 'mutasi lokasi',
        ];

        $parts = [];
        foreach ($labels as $key => $label) {
            if (!empty($counts[$key]['count'])) {
                $parts[] = $label . ' ' . $counts[$key]['count'];
            }
        }
        if (empty($parts)) {
            return null;
        }

        $summary = implode(', ', $parts);
        if ($isAdmin) {
            $locName = $admin?->location?->name;
            $locNote = $locName ? (' di lokasi ' . $locName) : '';
            $detailParts = [];
            foreach ($labels as $key => $label) {
                if (!empty($counts[$key]['names'])) {
                    $detailParts[] = ucfirst($label) . ': ' . $this->formatNameList($counts[$key]['names']);
                }
            }
            $detail = !empty($detailParts) ? ' Rincian ' . implode(' | ', $detailParts) . '.' : '';
            return 'Approval pending' . $locNote . ': ' . $summary . '.' . $detail;
        }

        return 'Notifikasi persetujuan: ' . $summary . '. Silakan menunggu atau koordinasi dengan Location Admin.';
    }

    private function shouldSendForLocation($location, string $settingKey): bool
    {
        $timezone = $location?->timezone ?? config('app.timezone', 'UTC');
        $now = Carbon::now($timezone);
        $configured = $location?->getSetting($settingKey, '00:00') ?? '00:00';
        $configured = $this->normalizeTime($configured);
        return $configured === $now->format('H:i');
    }

    private function normalizeTime(string $value): string
    {
        if (preg_match('/^([01]\\d|2[0-3]):([0-5]\\d)$/', $value)) {
            return $value;
        }
        return '00:00';
    }

    private function formatNameList(array $names): string
    {
        $names = array_values(array_unique(array_filter($names)));
        $total = count($names);
        $slice = array_slice($names, 0, 10);
        $text = implode(', ', $slice);
        if ($total > count($slice)) {
            $text .= ' dan ' . ($total - count($slice)) . ' lainnya';
        }
        return $text;
    }

    private function resolveSenderId(): ?int
    {
        $sender = User::role('Super Admin')->orderBy('id')->first()
            ?? User::orderBy('id')->first();
        return $sender?->id;
    }

    private function sendOncePerDay(int $senderId, int $receiverId, string $message, Carbon $date): bool
    {
        $exists = Message::where('sender_id', $senderId)
            ->where('receiver_id', $receiverId)
            ->where('message', $message)
            ->whereDate('created_at', $date->toDateString())
            ->exists();
        if ($exists) {
            return false;
        }

        Message::create([
            'sender_id' => $senderId,
            'receiver_id' => $receiverId,
            'message' => $message,
        ]);
        return true;
    }
}
