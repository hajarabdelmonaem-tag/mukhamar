<?php

namespace App\Services;

use App\Models\Setting;

class SettingsService
{
    /**
     * In-memory cache of the loaded settings.
     *
     * @var array<string, mixed>
     */
    private array $cache = [];

    /**
     * Get a setting value with an optional default.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        return $this->cache[$key] = Setting::query()
            ->where('key', $key)
            ->value('value') ?? $default;
    }

    /**
     * The active tax rate as a percentage.
     */
    public function taxRate(): float
    {
        return (float) $this->get('tax_rate', 15);
    }

    /**
     * The flat shipping fee for enabled orders.
     */
    public function shippingFee(): float
    {
        return (float) $this->get('shipping_fee', 30);
    }

    /**
     * The subtotal at which shipping becomes free.
     */
    public function freeShippingThreshold(): float
    {
        return (float) $this->get('free_shipping_threshold', 300);
    }

    /**
     * The currency symbol/code used for display.
     */
    public function currency(): string
    {
        return (string) $this->get('currency', 'SAR');
    }
}
