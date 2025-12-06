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
        'brand_name',
        'brand_logo_url',
        'code',
        'address',
        'timezone',
        'primary_color',
        'secondary_color',
        'latitude',
        'longitude',
        'radius',
        'settings',
        'custom_css_url',
        'custom_js_url',
        'is_active',
        'shift_enabled',
        'is_default',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'shift_enabled' => 'boolean',
        'is_default' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'radius' => 'decimal:2',
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
        return $this->belongsToMany(Shift::class, 'location_shifts')
            ->withPivot(['category', 'time_slots', 'is_default'])
            ->withTimestamps();
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
