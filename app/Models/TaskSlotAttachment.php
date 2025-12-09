<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSlotAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_slot_id',
        'type',
        'path_or_url',
        'uploaded_by',
    ];

    public function slot()
    {
        return $this->belongsTo(TaskSlot::class, 'task_slot_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
