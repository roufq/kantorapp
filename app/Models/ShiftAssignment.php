<?php

namespace App\Models;

use App\Models\Location;
use App\Models\LocationShift;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftAssignment extends Model
{
    protected $fillable = [
        'user_id',
        'location_id',
        'shift_id',
        'location_shift_id',
        'date',
        'status',
        'notes',
        'handover_required',
        'handover_note',
    ];

    protected $casts = [
        'date' => 'date',
        'handover_required' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function locationShift(): BelongsTo
    {
        return $this->belongsTo(LocationShift::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function scopeForLocation($query, $locationId)
    {
        return $query->where('location_id', $locationId);
    }
}
