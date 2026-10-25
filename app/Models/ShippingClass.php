<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ShippingClass extends Model
{
    protected $fillable = ['name', 'code', 'extra_charge', 'description', 'is_active'];

    protected $casts = [
        'extra_charge' => 'decimal:2',
        'is_active'    => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
