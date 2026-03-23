<?php

declare(strict_types=1);

namespace App\ViewModels\History;

/**
 * View model for the ticket options section containing multiple pricing cards.
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
