<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'email',
        'telepon',
        'alamat',
        'jabatan',
        'departemen',
        'tanggal_lahir',
        'tanggal_masuk_kerja',
        'divisi_id',
        'master_id',
        'location_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk_kerja' => 'date',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class, 'divisi_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'employee_id');
    }

    public function master()
    {
        return $this->belongsTo(User::class, 'master_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function jobdeskAssignments()
    {
        return $this->hasMany(EmployeeJobdeskAssignment::class);
    }

    public function jobdesks()
    {
        return $this->belongsToMany(Jobdesk::class, 'employee_jobdesk_assignments')
            ->withPivot(['is_primary', 'start_date', 'end_date', 'created_by'])
            ->withTimestamps();
    }

    public function positionHistories()
    {
        return $this->hasMany(EmployeePositionHistory::class);
    }

    public function transfers()
    {
        return $this->hasMany(EmployeeTransfer::class);
    }

    public function contracts()
    {
        return $this->hasMany(EmployeeContract::class);
    }

}
