<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class LocationShift extends Pivot
{
    protected $table = 'location_shifts';

    protected $fillable = [
        'location_id',
        'shift_id',
        'category',
        'time_slots',
        'is_default',
    ];

    public $incrementing = true;
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $casts = [
        'time_slots' => 'array',
        'is_default' => 'boolean',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Normalize slots to an array of [start, end, days[]].
     */
    public function normalizedSlots(): array
    {
        $slots = $this->time_slots ?? [];
        if (isset($slots['start']) && isset($slots['end'])) {
            $slots = [$slots];
        }

        $normalized = [];
        foreach ((array) $slots as $slot) {
            if (empty($slot['start']) || empty($slot['end'])) {
                continue;
            }
            $normalized[] = [
                'start' => $slot['start'],
                'end' => $slot['end'],
                'days' => isset($slot['days']) && is_array($slot['days'])
                    ? array_values(array_filter($slot['days']))
                    : [],
            ];
        }

        // Fallback to master shift slots when pivot slots are empty
        if (empty($normalized) && $this->shift) {
            $normalized = $this->shift->normalizedSlots();
        }

        return $normalized;
    }

    /**
     * Build Carbon intervals for the provided date using pivot slots (with fallback to shift slots).
     */
    public function slotIntervalsForDate(Carbon $date): array
    {
        $tz = $this->location ? ($this->location->timezone ?: config('app.timezone', 'UTC')) : config('app.timezone', 'UTC');
        $normalizedSlots = $this->normalizedSlots();
        $dayKey = strtolower($date->englishDayOfWeek);

        $intervals = [];
        foreach ($normalizedSlots as $slot) {
            if (!empty($slot['days'])) {
                $dayList = array_map('strtolower', $slot['days']);
                if (!in_array($dayKey, $dayList, true)) {
                    continue;
                }
            }

            $start = Carbon::parse($date->toDateString() . ' ' . $slot['start'], $tz);
            $end = Carbon::parse($date->toDateString() . ' ' . $slot['end'], $tz);
            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }

            $intervals[] = [$start, $end];
        }

        return $intervals;
    }

    /**
     * Get the earliest start time string for the provided date (used for lateness checks).
     */
    public function earliestStartForDate(Carbon $date): ?Carbon
    {
        $intervals = $this->slotIntervalsForDate($date);
        if (empty($intervals)) {
            return null;
        }

        usort($intervals, fn($a, $b) => $a[0]->gt($b[0]) ? 1 : -1);
        return $intervals[0][0];
    }
}
