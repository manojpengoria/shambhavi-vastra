<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'variant_type_name',
    'slug',
    'description',
    'used_for_variants',
    'status',
    'sort_order',
])]

/**
 * Attribute master. "Used for variants" attributes (Size, Color) build product
 * variants; the others (Fabric, Fit...) are product specifications.
 */
class VariantType extends Model
{
    protected function casts(): array
    {
        return [
            'used_for_variants' => 'boolean',
        ];
    }

    /**
     * Active attributes that build product variants (e.g. Size, Color).
     */
    public function scopeForVariants(Builder $query): Builder
    {
        return $query->where('status', 707)->where('used_for_variants', true);
    }

    /**
     * Active attributes used as product specifications (e.g. Fabric, Fit).
     */
    public function scopeForSpecifications(Builder $query): Builder
    {
        return $query->where('status', 707)->where('used_for_variants', false);
    }

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
