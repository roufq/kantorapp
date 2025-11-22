<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyOff extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id','user_id','day_of_week'
    ];
}

