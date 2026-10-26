<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warehouse extends Model
{
    protected $fillable = [
        'name', 'code', 'contact_person', 'mobile', 'email', 'address_line',
        'state_id', 'city_id', 'postal_code_id', 'area_id',
        'gstin', 'pickup_time_slot', 'is_default', 'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Ek hi default warehouse rahe
        static::saving(function (Warehouse $warehouse) {
            if ($warehouse->is_default) {
                static::where('id', '!=', $warehouse->id ?? 0)->update(['is_default' => false]);
            }
        });
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function postalCode(): BelongsTo
    {
        return $this->belongsTo(PostalCode::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
