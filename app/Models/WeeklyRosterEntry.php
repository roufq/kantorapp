<?php

namespace App\Models;

use App\Models\ShiftAssignment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyRosterEntry extends Model
{
    protected $fillable = [
        'weekly_roster_id',
        'shift_assignment_id',
        'user_id',
        'date',
        'slot_index',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function roster(): BelongsTo
    {
        return $this->belongsTo(WeeklyRoster::class, 'weekly_roster_id');
    }

    public function shiftAssignment(): BelongsTo
    {
        return $this->belongsTo(ShiftAssignment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
