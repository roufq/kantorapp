<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationWorkTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'employee_id',
        'year',
        'month',
        'target_minutes',
        'meta',
    ];

    protected $casts = [
        'target_minutes' => 'integer',
        'meta' => 'array',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
