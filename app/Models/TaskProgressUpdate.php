<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskProgressUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'progress',
        'photo_path',
        'document_path',
        'note',
        'approval_status',
        'approval_level',
        'requires_approval',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'progress' => 'integer',
        'requires_approval' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
