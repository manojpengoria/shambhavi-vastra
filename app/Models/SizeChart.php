<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class SizeChart extends Model
{
    public const STATUS_ACTIVE = 707;

    public const STATUS_INACTIVE = 505;

    public const MAX_COLUMNS = 10;

    public const MAX_ROWS = 30;

    /**
     * @var array<string, string>
     */
    public const UNITS = [
        'inch' => 'Inches',
        'cm' => 'Centimetres',
    ];

    protected $fillable = [
        'name',
        'unit',
        'columns',
        'rows',
        'how_to_measure',
        'image',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'columns' => 'array',
            'rows' => 'array',
            'status' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Public URL of the measuring guide image, if any.
     */
    public function imageUrl(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }
}
