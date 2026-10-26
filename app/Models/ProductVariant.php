<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'combination_key',
        'sku',
        'barcode',
        'product_price',
        'discount_price',
        'net_price',
        'weight',
        'quantity',
        'is_main',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'product_price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'net_price' => 'decimal:2',
            'weight' => 'decimal:3',
            'quantity' => 'integer',
            'is_main' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Variant values (e.g. Red, XL) that make up this combination.
     */
    public function values(): BelongsToMany
    {
        return $this->belongsToMany(VariantValue::class, 'product_variant_values')
            ->withPivot('variant_type_id')
            ->orderBy('product_variant_values.variant_type_id');
    }

    /**
     * Human readable combination, e.g. "Red / XL".
     */
    public function label(): string
    {
        return $this->values->pluck('variant_value_name')->implode(' / ');
    }
}
