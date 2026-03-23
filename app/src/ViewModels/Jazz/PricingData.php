<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * DTO for pricing section data.
 */
final readonly class PricingData
{
    /**
     * @param PricingCardData[] $pricingCards
     */
    public function __construct(
        public string $headingText,
        public string $subheadingText,
        public string $descriptionText,
        public array $pricingCards,
    ) {
    }
}
