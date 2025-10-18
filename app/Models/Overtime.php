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
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'duration_hours' => 'decimal:2',
        'selected_masters' => 'array',
    ];

    public function getDurationMinutesAttribute()
    {
        return $this->duration_hours * 60;
    }

    public function getStartTimeWibAttribute()
    {
        return $this->start_time ? $this->start_time->setTimezone('Asia/Jakarta')->format('H:i') : null;
    }

    public function getEndTimeWibAttribute()
    {
        return $this->end_time ? $this->end_time->setTimezone('Asia/Jakarta')->format('H:i') : null;
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
