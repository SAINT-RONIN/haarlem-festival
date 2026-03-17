<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

interface ICheckoutRuntimeConfig
{
    public function getAppUrl(): string;

    public function getVatRate(): float;
}
