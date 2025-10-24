<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftAssignment extends Model
{
    protected $fillable = [
        'user_id',
        'shift_id',
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

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function scopeForLocation($query, $locationId)
    {
        return $query->whereHas('user', function ($q) use ($locationId) {
            $q->where('location_id', $locationId);
        });
    }
}
