<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskCatalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'jobdesk_id',
        'name',
        'description',
        'unit',
        'value',
        'task_type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'value' => 'integer',
    ];

    public function jobdesk()
    {
        return $this->belongsTo(Jobdesk::class);
    }
}
