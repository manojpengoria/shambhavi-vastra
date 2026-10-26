<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Post office types keyed by code.
     *
     * @var array<string, string>
     */
    public const OFFICE_TYPES = [
        'HO' => 'Head Office',
        'SO' => 'Sub Office',
        'BO' => 'Branch Office',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }

    /**
     * Label used in searchable dropdowns, e.g. "302001 - Jaipur GPO".
     */
    public function selectLabel(): string
    {
        return $this->post_office_name
            ? "{$this->postal_code} - {$this->post_office_name}"
            : $this->postal_code;
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
