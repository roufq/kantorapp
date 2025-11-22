<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAbsence extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','location_id','date','type','notes'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function location() { return $this->belongsTo(Location::class); }
}

