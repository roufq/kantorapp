<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'location_id',
        'shift_id',
        'shift_assignment_id',
        'check_in_time',
        'check_out_time',
        'location',
        'is_late',
        'requires_approval',
        'approval_status',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'check_in_time' => 'datetime',
            'check_out_time' => 'datetime',
            'is_late' => 'boolean',
            'requires_approval' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function shiftAssignment()
    {
        return $this->belongsTo(ShiftAssignment::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
