<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'approver_id',
        'approver_role',
        'step_order',
        'status',
        'decided_at',
        'notes',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
