<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ApprovalRule;

class Task extends Model
{
    use HasFactory;

    protected $table = 'employee_tasks';

    protected $fillable = [
        'title',
        'description',
        'task_catalog_id',
        'assigned_by',
        'assigned_to',
        'status',
        'progress',
        'due_date',
        'photo_path',
        'document_path',
        'duration_minutes',
        'requires_approval',
        'approval_status',
        'approval_level',
        'approved_by',
        'approved_at',
        'approval_note',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'progress' => 'integer',
            'duration_minutes' => 'integer',
            'requires_approval' => 'boolean',
            'approved_at' => 'datetime',
        ];
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function catalog()
    {
        return $this->belongsTo(TaskCatalog::class, 'task_catalog_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function progressUpdates()
    {
        return $this->hasMany(TaskProgressUpdate::class);
    }

    public function slots()
    {
        return $this->hasMany(TaskSlot::class)->orderBy('order');
    }

    public function latestProgressUpdate()
    {
        return $this->hasOne(TaskProgressUpdate::class)->latestOfMany();
    }

    public function isAwaitingApproval(): bool
    {
        return $this->requires_approval && $this->approval_status === 'pending';
    }

    public function applyProgress($progress): void
    {
        $progress = (int) round($progress);
        $this->progress = max(0, min(100, $progress));
        $this->status = $this->progress >= 100 ? 'completed' : ($this->progress > 0 ? 'in_progress' : 'pending');
        $this->save();
    }

    public function recalcProgressFromSlots(): void
    {
        $approvedPercent = (float) $this->slots()->where('status', 'approved')->sum('percentage');
        $this->applyProgress($approvedPercent);
    }

    public function getSlotCompositionStatus(): array
    {
        $totalPercent = round((float) $this->slots()->sum('percentage'), 2);
        if (abs($totalPercent - 100) <= 0.05) {
            $totalPercent = 100.0;
        }

        $percentComplete = abs($totalPercent - 100) <= 0.01;
        $totalMinutes = (int) $this->slots()->sum('minutes');
        $durationMinutes = $this->duration_minutes ? (int) $this->duration_minutes : null;
        $minutesComplete = $durationMinutes === null ? true : $totalMinutes === $durationMinutes;

        return [
            'complete' => $percentComplete && $minutesComplete,
            'total_percent' => $totalPercent,
            'total_minutes' => $totalMinutes,
            'duration_minutes' => $durationMinutes,
        ];
    }

    public static function determineCreationApproval(User $creator, User $assignee, array $context = []): array
    {
        // Default: approved immediately
        $approved = [
            'requires_approval' => false,
            'approval_status' => 'approved',
            'approval_level' => 'none',
            'approved_by' => $creator->id,
            'approved_at' => now(),
            'approval_note' => null,
        ];

        $rule = null;
        $department = $context['department'] ?? null;
        $value = (int) ($context['value'] ?? 0);

        if ($value > 0) {
            $ruleQuery = ApprovalRule::active()
                ->where('scope', 'task')
                ->where('min_value', '<=', $value);
            if ($department) {
                $ruleQuery->where(function ($q) use ($department) {
                    $q->where('department', $department)->orWhereNull('department');
                })->orderByRaw('department is null');
            }
            $rule = $ruleQuery->orderByDesc('min_value')->first();
        }

        // If the creator assigns to someone else, keep auto-approval by default
        if ($creator->id !== $assignee->id) {
            if ($rule && !$creator->hasRole('Super Admin')) {
                $approvalLevel = $rule->approval_level;
                if ($approvalLevel === 'location_admin') {
                    $locationAdmin = $creator->location_id
                        ? User::role('Admin Lokasi')->where('location_id', $creator->location_id)->first()
                        : null;
                    $approvalLevel = $locationAdmin ? 'location_admin' : 'super_admin';
                }
                return [
                    'requires_approval' => true,
                    'approval_status' => 'pending',
                    'approval_level' => $approvalLevel,
                    'approved_by' => null,
                    'approved_at' => null,
                    'approval_note' => null,
                ];
            }
            return $approved;
        }

        // Self-assigned by Super Admin -> auto approved
        if ($creator->hasRole('Super Admin')) {
            return $approved;
        }

        // Self-assigned by Admin Lokasi -> requires Super Admin approval
        if ($creator->hasRole('Admin Lokasi')) {
            return [
                'requires_approval' => true,
                'approval_status' => 'pending',
                'approval_level' => 'super_admin',
                'approved_by' => null,
                'approved_at' => null,
                'approval_note' => null,
            ];
        }

        // Self-assigned by Karyawan (or other roles) -> prefer Location Admin in same location, fallback to Super Admin
        $locationAdmin = $creator->location_id
            ? User::role('Admin Lokasi')->where('location_id', $creator->location_id)->first()
            : null;

        $hasLocationAdmin = (bool) $locationAdmin;

        $meta = [
            'requires_approval' => true,
            'approval_status' => 'pending',
            'approval_level' => $hasLocationAdmin ? 'location_admin' : 'super_admin',
            'approved_by' => null,
            'approved_at' => null,
            'approval_note' => null,
        ];

        if ($rule && $rule->approval_level === 'super_admin') {
            $meta['approval_level'] = 'super_admin';
        }

        return $meta;
    }
}
