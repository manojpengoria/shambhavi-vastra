<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'parent_id',
        'category_name',
        'thumb_image',
        'large_image',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'seo_url',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'parent_id' => 'integer',
            'status' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Parent category.
     * Null parent means this is a main category.
    */

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Direct subcategories.
    */

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('category_name');
    }

    /**
     * All descendant categories.
    */

    public function descendants(): HasMany
    {
        return $this->children()->with('descendants');
    }

    /**
     * Scope for active categories.
    */

    public function scopeActive($query)
    {
        return $query->where('status', 707);
    }

    /**
     * Scope for main categories.
    */

    public function scopeMainCategories($query)
    {
        return $query->whereNull('parent_id');
    }
}
