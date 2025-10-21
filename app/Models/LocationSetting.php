<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationSetting extends Model
{
    protected $fillable = [
        'location_id',
        'key',
        'value',
        'type',
        'description',
    ];

    protected $casts = [
        'value' => 'json',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function getValue()
    {
        switch ($this->type) {
            case 'boolean':
                return (bool) $this->value;
            case 'number':
                return is_numeric($this->value) ? (float) $this->value : $this->value;
            case 'json':
                return is_string($this->value) ? json_decode($this->value, true) : $this->value;
            default:
                return $this->value;
        }
    }

    public function setValue($value): void
    {
        $this->value = json_encode($value);
    }
}
