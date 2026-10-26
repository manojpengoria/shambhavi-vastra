<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    public const STATUS_VISIBLE = 707;

    public const STATUS_HIDDEN = 505;

    public const MAX_IMAGES = 12;

    /**
     * Product labels keyed by stored value.
     *
     * @var array<string, string>
     */
    public const LABELS = [
        'popular' => 'Popular',
        'new' => 'New',
        'custom' => 'Custom',
    ];

    protected $fillable = [
        'product_name',
        'sku',
        'barcode',
        'hsn_sac_code',
        'short_summary_brand',
        'summary',
        'description',
        'origin_country',
        'youtube_video',
        'selling_price',
        'discount',
        'net_price',
        'purchase_cost',
        'moq',
        'taxable',
        'tax_rate',
        'track_inventory',
        'stock_quantity',
        'has_variants',
        'weight',
        'length',
        'breadth',
        'height',
        'shipping_class_id',
        'shipping_package_id',
        'free_shipping',
        'cod_available',
        'shipping_days',
        'out_of_stock_days',
        'is_returnable',
        'return_days',
        'label',
        'custom_label',
        'seo_url',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'status',
        'is_draft',
        'publish_at',
        'visible_after_login',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'selling_price' => 'decimal:2',
            'discount' => 'decimal:2',
            'net_price' => 'decimal:2',
            'purchase_cost' => 'decimal:2',
            'moq' => 'integer',
            'taxable' => 'boolean',
            'tax_rate' => 'decimal:2',
            'track_inventory' => 'boolean',
            'stock_quantity' => 'integer',
            'has_variants' => 'boolean',
            'weight' => 'decimal:3',
            'length' => 'decimal:2',
            'breadth' => 'decimal:2',
            'height' => 'decimal:2',
            'free_shipping' => 'boolean',
            'cod_available' => 'boolean',
            'shipping_days' => 'integer',
            'out_of_stock_days' => 'integer',
            'is_returnable' => 'boolean',
            'return_days' => 'integer',
            'status' => 'integer',
            'is_draft' => 'boolean',
            'publish_at' => 'datetime',
            'visible_after_login' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function mainImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_main', true);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order');
    }

    public function shippingClass(): BelongsTo
    {
        return $this->belongsTo(ShippingClass::class);
    }

    public function shippingPackage(): BelongsTo
    {
        return $this->belongsTo(ShippingPackage::class);
    }

    /**
     * Products that are published, visible and past their publish date.
     */
    public function scopeLive(Builder $query): Builder
    {
        return $query->where('is_draft', false)
            ->where('status', self::STATUS_VISIBLE)
            ->where(fn (Builder $query) => $query->whereNull('publish_at')->orWhere('publish_at', '<=', now()));
    }
}
