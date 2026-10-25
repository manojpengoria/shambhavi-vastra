<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ShippingZone extends Model
{
    public const COVERAGE_STATE = 'state';
    public const COVERAGE_CITY  = 'city';
    public const COVERAGE_ALL   = 'all';

    public const COVERAGE_TYPES = [
        self::COVERAGE_STATE => 'State',
        self::COVERAGE_CITY  => 'City',
        self::COVERAGE_ALL   => 'All (Rest of India)',
    ];

    protected $fillable = ['name', 'code', 'coverage_type', 'priority', 'description', 'is_active'];

    protected $casts = [
        'priority'  => 'integer',
        'is_active' => 'boolean',
    ];

    public function states(): BelongsToMany
    {
        return $this->belongsToMany(State::class, 'shipping_zone_state');
    }

    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'shipping_zone_city');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Pincode ki state/city se zone nikalna.
     * Priority chhoti = pehle match. Jaipur "Local Jaipur" (city, 1) aur
     * "Rest of Rajasthan" (state, 2) dono me hai, to Local Jaipur milega.
     */
    public static function resolveFor(int $stateId, int $cityId): ?self
    {
        return static::active()
            ->where(function (Builder $q) use ($stateId, $cityId) {
                $q->where('coverage_type', self::COVERAGE_ALL)
                  ->orWhere(fn (Builder $c) => $c->where('coverage_type', self::COVERAGE_CITY)
                        ->whereHas('cities', fn (Builder $x) => $x->where('cities.id', $cityId)))
                  ->orWhere(fn (Builder $s) => $s->where('coverage_type', self::COVERAGE_STATE)
                        ->whereHas('states', fn (Builder $x) => $x->where('states.id', $stateId)));
            })
            ->orderBy('priority')
            ->first();
    }
}
