<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'name',
        'percentage',
        'minutes',
        'order',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'created_by',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'minutes' => 'integer',
        'order' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attachments()
    {
        return $this->hasMany(TaskSlotAttachment::class);
    }

    public function history()
    {
        return $this->hasMany(TaskSlotHistory::class);
    }
}
