<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'variant_type_name',
    'slug',
    'description',
    'status',
    'sort_order',
])]

class VariantType extends Model
{
    public function values(): HasMany
    {
        return $this->hasMany(VariantValue::class, 'variant_type_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function activeValues(): HasMany
    {
        return $this->hasMany(VariantValue::class, 'variant_type_id')
            ->where('status', 707)
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
