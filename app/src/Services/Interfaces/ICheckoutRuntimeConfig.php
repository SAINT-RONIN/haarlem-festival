<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

/**
 * Defines runtime configuration values required by the checkout process.
 */
interface ICheckoutRuntimeConfig
{
    /**
     * Returns the base application URL used for building Stripe callback URLs.
     */
    public function getAppUrl(): string;

    /**
     * Returns the current VAT rate as a decimal (e.g. 0.21 for 21%).
     */
    public function getVatRate(): float;
}
