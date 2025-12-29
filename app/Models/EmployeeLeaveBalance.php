<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLeaveBalance extends Model
{
    protected $fillable = [
        'user_id',
        'year',
        'annual_quota',
        'carry_over',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
