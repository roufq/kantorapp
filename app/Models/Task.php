<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $table = 'employee_tasks';

    protected $fillable = [
        'title',
        'description',
        'assigned_by',
        'assigned_to',
        'status',
        'progress',
        'due_date',
        'photo_path',
        'document_path',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'progress' => 'integer',
        ];
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function progressUpdates()
    {
        return $this->hasMany(TaskProgressUpdate::class);
    }

    public function latestProgressUpdate()
    {
        return $this->hasOne(TaskProgressUpdate::class)->latestOfMany();
    }

    public function applyProgress(int $progress): void
    {
        $this->progress = max(0, min(100, $progress));
        $this->status = $this->progress >= 100 ? 'completed' : ($this->progress > 0 ? 'in_progress' : 'pending');
        $this->save();
    }
}
