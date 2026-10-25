<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostalCode extends Model
{
    protected $fillable = [
        'city_id',
        'postal_code',
        'post_office_name',
        'office_type',
        'delivery_status',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'delivery_status' => 'integer',
            'status' => 'integer',
        ];
    }

    public const STATUS_ENABLED = 707;

    public const STATUS_DISABLED = 505;

    public const DELIVERY_AVAILABLE = 707;

    public const DELIVERY_UNAVAILABLE = 505;

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }

    public function scopeDeliveryAvailable($query)
    {
        return $query->where(
            'delivery_status',
            self::DELIVERY_AVAILABLE
        );
    }
}
