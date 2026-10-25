<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class State extends Model
{
    protected $fillable = [
        'country_id',
        'state_name',
        'state_code',
        'gst_state_code',
        'state_type',
        'capital',
        'status',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public const STATUS_ENABLED = 707;

    public const STATUS_DISABLED = 505;

    public const TYPE_STATE = 'STATE';

    public const TYPE_UT = 'UT';

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function scopeEnabled($query)
    {
        return $query->where('status', self::STATUS_ENABLED);
    }

    public function scopeDisabled($query)
    {
        return $query->where('status', self::STATUS_DISABLED);
    }

    public function scopeStates($query)
    {
        return $query->where('state_type', self::TYPE_STATE);
    }

    public function scopeUnionTerritories($query)
    {
        return $query->where('state_type', self::TYPE_UT);
    }
}
