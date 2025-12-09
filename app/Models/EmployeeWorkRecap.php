<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeWorkRecap extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'location_id',
        'year',
        'month',
        'slot_minutes_approved',
        'attendance_minutes',
        'total_minutes',
        'meta',
    ];

    protected $casts = [
        'slot_minutes_approved' => 'integer',
        'attendance_minutes' => 'integer',
        'total_minutes' => 'integer',
        'meta' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
