<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class ShippingSetting extends Model
{
    public const PAID_BY_CUSTOMER = 'customer';
    public const PAID_BY_SELLER   = 'seller';

    public const RETURN_PAID_BY = [
        self::PAID_BY_CUSTOMER => 'Customer',
        self::PAID_BY_SELLER   => 'Seller',
    ];

    protected const CACHE_KEY = 'shipping_settings';

    protected $fillable = [
        'default_warehouse_id', 'default_courier_partner_id', 'volumetric_divisor',
        'free_shipping_above', 'cod_enabled', 'cod_min_order', 'cod_max_order',
        'return_window_days', 'return_shipping_paid_by',
        'show_delivery_date', 'pincode_check_before_cart',
    ];

    protected $casts = [
        'volumetric_divisor'        => 'integer',
        'free_shipping_above'       => 'decimal:2',
        'cod_enabled'               => 'boolean',
        'cod_min_order'             => 'decimal:2',
        'cod_max_order'             => 'decimal:2',
        'return_window_days'        => 'integer',
        'show_delivery_date'        => 'boolean',
        'pincode_check_before_cart' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
    }

    /** Hamesha yahi use karein: ShippingSetting::current()->volumetric_divisor */
    public static function current(): self
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => static::firstOrCreate(['id' => 1]));
    }

    public function defaultWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'default_warehouse_id');
    }

    public function defaultCourierPartner(): BelongsTo
    {
        return $this->belongsTo(CourierPartner::class, 'default_courier_partner_id');
    }
}
