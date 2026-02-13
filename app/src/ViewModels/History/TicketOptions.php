<?php

declare(strict_types=1);

namespace App\ViewModels\History;

/**
 * DTO for schedule section data.
 */
final readonly class TicketOptions
{
    /**
     * @param PricingCard[] $pricingCards
     */
    public function __construct(
        public string $headingText,
        public array $pricingCards,
    ) {
    }
}
