<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    protected $fillable = [
        'state_id',
        'city_name',
        'city_code',
        'city_type',
        'latitude',
        'longitude',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'status' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public const STATUS_ENABLED = 707;

    public const STATUS_DISABLED = 505;

    public const TYPE_CITY = 'CITY';

    public const TYPE_TOWN = 'TOWN';

    public const TYPE_VILLAGE = 'VILLAGE';

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function postalCodes(): HasMany
    {
        return $this->hasMany(PostalCode::class);
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }

    /**
     * Label used in searchable dropdowns, e.g. "Jaipur (Rajasthan)".
     */
    public function selectLabel(): string
    {
        return "{$this->city_name} ({$this->state->state_name})";
    }

    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }

    public function scopeDisabled($query)
    {
        return $query->where('status', self::STATUS_DISABLED);
    }
}
