<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Services\Interfaces\ICheckoutRuntimeConfig;

final class CheckoutRuntimeConfig implements ICheckoutRuntimeConfig
{
    private string $appUrl;
    private float $vatRate;

    public function __construct(
        string $appUrl,
        float $vatRate,
    ) {
        $normalizedAppUrl = rtrim($appUrl, '/');
        if ($normalizedAppUrl === '') {
            throw new \RuntimeException('APP_URL environment variable is required for Stripe checkout redirects.');
        }

        if ($vatRate < 0 || $vatRate > 1) {
            throw new \RuntimeException('VAT_RATE must be between 0 and 1.');
        }

        $this->appUrl = $normalizedAppUrl;
        $this->vatRate = $vatRate;
    }

    public function getAppUrl(): string
    {
        return $this->appUrl;
    }

    public function getVatRate(): float
    {
        return $this->vatRate;
    }
}
