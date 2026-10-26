<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    /**
     * Form fields stored in their own columns; every other field goes into the encrypted settings.
     *
     * @var array<int, string>
     */
    public const COLUMN_FIELDS = [
        'instructions',
        'min_order_amount',
        'max_order_amount',
        'display_options',
        'show_icon',
        'sort_order',
    ];

    protected $fillable = [
        'code',
        'is_active',
        'instructions',
        'min_order_amount',
        'max_order_amount',
        'display_options',
        'show_icon',
        'sort_order',
        'settings',
        'activated_at',
    ];

    /**
     * Gateway credentials must never leak into arrays / JSON.
     *
     * @var array<int, string>
     */
    protected $hidden = ['settings'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'min_order_amount' => 'decimal:2',
            'max_order_amount' => 'decimal:2',
            'display_options' => 'array',
            'show_icon' => 'boolean',
            'sort_order' => 'integer',
            'settings' => 'encrypted:array',
            'activated_at' => 'datetime',
        ];
    }

    /**
     * Active methods in checkout order.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Definition of this method from config/payment_methods.php.
     *
     * @return array<string, mixed>|null
     */
    public function definition(): ?array
    {
        return config("payment_methods.methods.{$this->code}");
    }

    /**
     * Saved value of a form field, from its column or the encrypted settings.
     */
    public function fieldValue(string $field): mixed
    {
        if (in_array($field, self::COLUMN_FIELDS, true)) {
            return $this->getAttribute($field);
        }

        return $this->settings[$field] ?? null;
    }

    /**
     * Validation message for a required field, e.g. "Merchant ID" -> "Please enter merchant ID".
     * Words with a capital letter after the first character (ID, API, iOS) keep their case.
     */
    public static function requiredMessage(string $label): string
    {
        return 'Please enter '.preg_replace_callback(
            '/\b\w+\b/',
            fn (array $word) => preg_match('/^.+[A-Z]/', $word[0]) ? $word[0] : strtolower($word[0]),
            $label
        );
    }
}
