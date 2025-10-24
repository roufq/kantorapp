<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Shift extends Model
{
    protected $fillable = [
        'name',
        'code',
        'day',
        'shift_type',
        'time_slots',
        'is_active',
        'description',
        'break_minutes',
    ];

    protected $casts = [
        'time_slots' => 'array', // JSON cast for flexible time slots
        'is_active' => 'boolean',
        'break_minutes' => 'integer',
    ];

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'location_shifts');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(ShiftAssignment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForLocation($query, $locationId)
    {
        return $query->whereHas('locations', function ($q) use ($locationId) {
            $q->where('locations.id', $locationId);
        });
    }

    // Helper methods for time slots
    public function getTimeSlotsAttribute($value)
    {
        if ($this->shift_type === 'multiple') {
            // Return array of time slots for multiple shifts
            return json_decode($value, true) ?? [];
        } else {
            // Return single time slot for single shifts
            $decoded = json_decode($value, true);
            return $decoded ?? ['start' => '09:00', 'end' => '17:00'];
        }
    }

    public function setTimeSlotsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['time_slots'] = json_encode($value);
        } else {
            $this->attributes['time_slots'] = $value;
        }
    }

    // Check if shift is multiple type
    public function isMultipleShift()
    {
        return $this->shift_type === 'multiple';
    }

    // Check if shift is single type
    public function isSingleShift()
    {
        return $this->shift_type === 'single';
    }

    // Backward compatibility methods
    public function getStartTimeAttribute()
    {
        if ($this->isSingleShift() && isset($this->time_slots['start'])) {
            return Carbon::createFromFormat('H:i', $this->time_slots['start']);
        }
        return null;
    }

    public function getEndTimeAttribute()
    {
        if ($this->isSingleShift() && isset($this->time_slots['end'])) {
            return Carbon::createFromFormat('H:i', $this->time_slots['end']);
        }
        return null;
    }

    public function isCurrentlyActive(?string $timezone = null): bool
    {
        if ($this->isMultipleShift()) {
            // For multiple shifts, check if any slot is active
            $now = Carbon::now($timezone ?? 'Asia/Jakarta');
            $currentTime = $now->format('H:i');

            foreach ($this->time_slots as $slot) {
                if (isset($slot['start']) && isset($slot['end'])) {
                    if ($currentTime >= $slot['start'] && $currentTime <= $slot['end']) {
                        return true;
                    }
                }
            }
            return false;
        } else {
            // For single shifts, use existing logic
            $now = Carbon::now($timezone ?? 'Asia/Jakarta');
            $currentTime = $now->format('H:i');

            return isset($this->time_slots['start']) && isset($this->time_slots['end']) &&
                   $currentTime >= $this->time_slots['start'] &&
                   $currentTime <= $this->time_slots['end'];
        }
    }

    public function getDurationInMinutes(): int
    {
        if ($this->isMultipleShift()) {
            // For multiple shifts, sum all durations
            $totalMinutes = 0;
            foreach ($this->time_slots as $slot) {
                if (isset($slot['start']) && isset($slot['end'])) {
                    $start = Carbon::createFromFormat('H:i', $slot['start']);
                    $end = Carbon::createFromFormat('H:i', $slot['end']);

                    // Handle overnight shifts
                    if ($end->lessThan($start)) {
                        $end->addDay();
                    }

                    $totalMinutes += $start->diffInMinutes($end);
                }
            }
            return $totalMinutes;
        } else {
            // For single shifts, use existing logic
            if (!isset($this->time_slots['start']) || !isset($this->time_slots['end'])) {
                return 0;
            }

            $start = Carbon::createFromFormat('H:i', $this->time_slots['start']);
            $end = Carbon::createFromFormat('H:i', $this->time_slots['end']);

            // Handle overnight shifts
            if ($end->lessThan($start)) {
                $end->addDay();
            }

            return $start->diffInMinutes($end);
        }
    }

    public function getDayName(): string
    {
        return ucfirst($this->day ?? 'Not specified');
    }

    public function getFormattedSchedule(): string
    {
        if ($this->isMultipleShift()) {
            $schedules = [];
            foreach ($this->time_slots as $slot) {
                if (isset($slot['start']) && isset($slot['end'])) {
                    $schedules[] = $slot['start'] . ' - ' . $slot['end'];
                }
            }
            return implode(', ', $schedules);
        } else {
            return isset($this->time_slots['start']) && isset($this->time_slots['end'])
                ? $this->time_slots['start'] . ' - ' . $this->time_slots['end']
                : 'Not specified';
        }
    }
}
