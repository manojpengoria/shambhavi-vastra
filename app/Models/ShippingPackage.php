<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ShippingPackage extends Model
{
    protected $fillable = [
        'name', 'code', 'length', 'breadth', 'height',
        'box_weight', 'max_weight', 'cost', 'is_active',
    ];

    protected $casts = [
        'length'     => 'decimal:2',
        'breadth'    => 'decimal:2',
        'height'     => 'decimal:2',
        'box_weight' => 'decimal:3',
        'max_weight' => 'decimal:3',
        'cost'       => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    /** L x B x H / divisor (settings se, default 5000) => kg */
    public function getVolumetricWeightAttribute(): float
    {
        $divisor = ShippingSetting::current()->volumetric_divisor ?: 5000;

        return round(($this->length * $this->breadth * $this->height) / $divisor, 3);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
