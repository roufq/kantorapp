<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskSlotHistory extends Model
{
    use HasFactory;

    protected $table = 'task_slot_history';

    protected $fillable = [
        'task_slot_id',
        'action',
        'data_before',
        'data_after',
        'actor_id',
    ];

    protected $casts = [
        'data_before' => 'array',
        'data_after' => 'array',
    ];

    public function slot()
    {
        return $this->belongsTo(TaskSlot::class, 'task_slot_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
