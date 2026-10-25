<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'country_name',
        'country_code',
        'dial_code',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
        ];
    }

    /**
     * Status constants.
     */

    public const STATUS_ENABLED = 707;

    public const STATUS_DISABLED = 505;
    /**
     * Scope for enabled countries.
     */

    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }

    /**
     * Scope for disabled countries.
     */

    public function scopeDisabled($query)
    {
        return $query->where('status', self::STATUS_DISABLED);
    }
}
