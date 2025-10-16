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
        'divisi_id',
        'master_id',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
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
}
