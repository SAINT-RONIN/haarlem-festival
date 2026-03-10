<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * DTO for single pricing card data.
 */
final readonly class PricingCardData
{
    /**
     * @param string[] $items
     * @param string[] $includes
     */
    public function __construct(
        public string $title,
        public string $price,
        public string $priceDescription,
        public array $items,
        public array $includes,
        public string $additionalInfo,
        public bool $isHighlighted = false,
    ) {
    }
}
