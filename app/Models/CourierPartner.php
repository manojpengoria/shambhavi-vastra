<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CourierPartner extends Model
{
    public const TYPE_API    = 'api';
    public const TYPE_MANUAL = 'manual';

    public const INTEGRATION_TYPES = [
        self::TYPE_API    => 'API',
        self::TYPE_MANUAL => 'Manual',
    ];

    protected $fillable = [
        'name', 'code', 'logo', 'integration_type', 'api_key', 'api_secret',
        'tracking_url', 'priority', 'max_weight', 'max_cod_amount',
        'supports_cod', 'supports_reverse_pickup', 'supports_express', 'is_active',
    ];

    protected $casts = [
        'api_key'                 => 'encrypted', // DB me encrypt hokar save
        'api_secret'              => 'encrypted',
        'priority'                => 'integer',
        'max_weight'              => 'decimal:3',
        'max_cod_amount'          => 'decimal:2',
        'supports_cod'            => 'boolean',
        'supports_reverse_pickup' => 'boolean',
        'supports_express'        => 'boolean',
        'is_active'               => 'boolean',
    ];

    // API response / JSON me keys kabhi na jaayein
    protected $hidden = ['api_key', 'api_secret'];

    /** {AWB} ki jagah asli AWB number laga kar tracking link */
    public function trackingLink(?string $awb): ?string
    {
        if (! $this->tracking_url || ! $awb) {
            return null;
        }

        return str_replace('{AWB}', urlencode($awb), $this->tracking_url);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('priority');
    }
}
