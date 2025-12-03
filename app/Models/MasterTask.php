<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterTask extends Model
{
    use HasFactory;

    protected $table = 'master_tasks';

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
}
