<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeeklyRoster extends Model
{
    protected $fillable = [
        'location_id',
        'location_shift_id',
        'week_start',
        'week_end',
        'locked',
        'meta',
    ];

    protected $casts = [
        'week_start' => 'date',
        'week_end' => 'date',
        'locked' => 'boolean',
        'meta' => 'array',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function locationShift(): BelongsTo
    {
        return $this->belongsTo(LocationShift::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(WeeklyRosterEntry::class);
    }
}
