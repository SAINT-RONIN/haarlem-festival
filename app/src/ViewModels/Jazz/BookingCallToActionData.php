<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * CTA banner data for the jazz page booking section.
 */
final readonly class BookingCallToActionData
{
    /**
     * @param BookingCardData[] $cards
     */
    public function __construct(
        public string $headingText,
        public string $descriptionText,
        public array $cards,
    ) {
    }
}
