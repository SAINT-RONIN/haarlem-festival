<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * A single line item inside a pricing card — name, price, and optional capacity info.
 */
final readonly class PricingCardItemData
{
    public function __construct(
        public string $name,
        public string $price,
        public string $capacity,
    ) {}
}
