<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobdesk extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'role_scope',
        'location_id',
        'created_by',
        'is_active',
        'min_attendance_minutes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_attendance_minutes' => 'integer',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function taskCatalogs()
    {
        return $this->hasMany(TaskCatalog::class);
    }

    public function assignments()
    {
        return $this->hasMany(EmployeeJobdeskAssignment::class);
    }
}
