<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'variant_type_id',
    'variant_value_name',
    'slug',
    'color_code',
    'status',
    'sort_order',
])]

class VariantValue extends Model
{
    public function variantType(): BelongsTo
    {
        return $this->belongsTo(VariantType::class, 'variant_type_id');
    }
}
