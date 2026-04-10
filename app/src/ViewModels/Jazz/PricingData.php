<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * Section data for the jazz page pricing section — heading and array of pricing cards.
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
    ) {}
}
