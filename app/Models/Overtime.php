<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    use HasFactory;

    protected $table = 'overtime_requests';

    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'duration_hours',
        'reason',
        'status',
        'selected_masters',
    ];

    protected $casts = [
        'date' => 'date',
        // Kolom bertipe TIME di DB; gunakan string agar tidak terjadi parsing ganda pada Carbon
        'start_time' => 'string',
        'end_time' => 'string',
        'duration_hours' => 'decimal:2',
        'selected_masters' => 'array',
    ];

    public function getDurationMinutesAttribute()
    {
        return $this->duration_hours * 60;
    }

    public function getStartTimeWibAttribute()
    {
        if (!$this->start_time) return null;
        // start_time string kemungkinan 'HH:MM:SS' atau 'HH:MM'
        $t = substr((string) $this->start_time, 0, 5);
        return $t;
    }

    public function getEndTimeWibAttribute()
    {
        if (!$this->end_time) return null;
        $t = substr((string) $this->end_time, 0, 5);
        return $t;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvals()
    {
        return $this->hasMany(OvertimeApproval::class, 'overtime_request_id');
    }

    public function isApprovedByMaster($masterId)
    {
        return $this->approvals()->where('master_id', $masterId)->where('status', 'approved')->exists();
    }

    public function isRejectedByMaster($masterId)
    {
        return $this->approvals()->where('master_id', $masterId)->where('status', 'rejected')->exists();
    }

    public function getApprovalStatusForMaster($masterId)
    {
        $approval = $this->approvals()->where('master_id', $masterId)->first();
        return $approval ? $approval->status : 'pending';
    }

    public function updateOverallStatus()
    {
        $selectedMasters = $this->selected_masters;
        $approvedCount = 0;
        $rejectedCount = 0;

        foreach ($selectedMasters as $masterId) {
            $status = $this->getApprovalStatusForMaster($masterId);
            if ($status === 'approved') {
                $approvedCount++;
            } elseif ($status === 'rejected') {
                $rejectedCount++;
            }
        }

        if ($rejectedCount > 0) {
            $this->status = 'rejected';
        } elseif ($approvedCount === count($selectedMasters)) {
            $this->status = 'approved';
        } else {
            $this->status = 'pending';
        }

        $this->save();
    }
}
