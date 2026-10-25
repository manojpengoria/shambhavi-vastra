<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    protected $fillable = ['name', 'code', 'customer_text', 'display_order', 'is_active'];

    protected $casts = [
        'display_order' => 'integer',
        'is_active'     => 'boolean',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('display_order');
    }
}
