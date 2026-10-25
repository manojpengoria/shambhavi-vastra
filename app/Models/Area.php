<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Area extends Model
{
    protected $fillable = [
        'city_id',
        'postal_code_id',
        'area_name',
        'area_code',
        'area_type',
        'latitude',
        'longitude',
        'delivery_available',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'delivery_available' => 'integer',
            'status' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public const STATUS_ENABLED = 707;

    public const STATUS_DISABLED = 505;

    public const DELIVERY_AVAILABLE = 707;

    public const DELIVERY_UNAVAILABLE = 505;

    public const TYPE_LOCALITY = 'LOCALITY';

    public const TYPE_COLONY = 'COLONY';

    public const TYPE_SECTOR = 'SECTOR';

    public const TYPE_VILLAGE = 'VILLAGE';

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function postalCode(): BelongsTo
    {
        return $this->belongsTo(PostalCode::class);
    }

    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }

    public function scopeDeliveryAvailable($query)
    {
        return $query->where(
            'delivery_available',
            self::DELIVERY_AVAILABLE
        );
    }
}
