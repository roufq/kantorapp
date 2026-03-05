<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeJobdeskAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'jobdesk_id',
        'is_primary',
        'start_date',
        'end_date',
        'created_by',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function jobdesk()
    {
        return $this->belongsTo(Jobdesk::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
