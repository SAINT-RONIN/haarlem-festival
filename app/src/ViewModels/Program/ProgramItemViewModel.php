<?php

declare(strict_types=1);

namespace App\ViewModels\Program;

/**
 * A single line item in the my-program page.
 *
 * Pre-formatted event title, session time, price, and quantity controls.
 */
final readonly class ProgramItemViewModel
{
    public function __construct(
        public int $programItemId,
        public ?int $eventSessionId,
        public string $eventTitle,
        public string $locationDisplay,
        public string $dateTimeDisplay,
        public string $priceDisplay,
        public float $rawPrice,
        public int $quantity,
        public float $donationAmount,
        public string $donationDisplay,
        public string $sumDisplay,
        public string $eventTypeSlug,
        public string $eventTypeLabel,
        public string $eventTypeImageUrl,
        public bool $isPayWhatYouLike,
        public ?string $languageLabel,
        public ?string $ageLabel,
        public bool $isReservation = false,
    ) {
    }
}
