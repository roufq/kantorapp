<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeePositionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'title',
        'department',
        'start_date',
        'end_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
