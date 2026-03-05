<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobdeskOutputTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'jobdesk_id',
        'employee_id',
        'year',
        'month',
        'unit',
        'target_value',
    ];

    protected $casts = [
        'year' => 'integer',
        'month' => 'integer',
        'target_value' => 'integer',
    ];

    public function jobdesk()
    {
        return $this->belongsTo(Jobdesk::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
