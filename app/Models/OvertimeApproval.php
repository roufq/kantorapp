<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OvertimeApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'overtime_request_id',
        'master_id',
        'level',
        'status',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'level' => 'integer',
    ];

    public function overtimeRequest()
    {
        return $this->belongsTo(Overtime::class, 'overtime_request_id');
    }

    public function master()
    {
        return $this->belongsTo(User::class, 'master_id');
    }
}
