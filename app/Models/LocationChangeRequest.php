<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocationChangeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_location_id',
        'target_location_id',
        'reason',
        'status',
        'is_permanent',
        'request_date',
        'approved_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function originalLocation()
    {
        return $this->belongsTo(Location::class, 'original_location_id');
    }

    public function targetLocation()
    {
        return $this->belongsTo(Location::class, 'target_location_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
