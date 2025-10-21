<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Location extends Model
{
    protected $fillable = [
        'name',
        'code',
        'address',
        'timezone',
        'latitude',
        'longitude',
        'radius',
        'settings',
        'is_active',
        'shift_enabled',
        'schedule_type',
        'daily_schedule',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'shift_enabled' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius' => 'decimal:2',
        'daily_schedule' => 'array',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function locationSettings(): HasMany
    {
        return $this->hasMany(LocationSetting::class);
    }

    public function employees(): HasManyThrough
    {
        return $this->hasManyThrough(Employee::class, User::class, 'location_id', 'id', 'id', 'karyawan_id');
    }

    public function shifts(): BelongsToMany
    {
        return $this->belongsToMany(Shift::class, 'location_shifts');
    }

    public function getSetting(string $key, $default = null)
    {
        $setting = $this->locationSettings()->where('key', $key)->first();
        return $setting ? $setting->getValue() : $default;
    }

    public function setSetting(string $key, $value, string $type = 'string'): void
    {
        $this->locationSettings()->updateOrCreate(
            ['key' => $key],
            ['value' => json_encode($value), 'type' => $type]
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
