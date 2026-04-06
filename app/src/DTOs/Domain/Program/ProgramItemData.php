<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Program;

/**
 * A single program item with resolved event/session details for display.
 */
final readonly class ProgramItemData
{
    public function __construct(
        public int $programItemId,
        public ?int $eventSessionId = null,
        public int $quantity = 1,
        public float $donationAmount = 0.0,
        public string $eventTitle = '',
        public ?string $venueName = null,
        public ?string $hallName = null,
        public string $startDateTime = '',
        public ?string $endDateTime = null,
        public int $eventTypeId = 0,
        public string $eventTypeName = '',
        public string $eventTypeSlug = '',
        public ?string $languageCode = null,
        public ?int $minAge = null,
        public ?int $maxAge = null,
        public bool $isPayWhatYouLike = false,
        public float $basePrice = 0.0,
        public ?string $priceTier = null,
        public ?int $passTypeId = null,
        public ?string $passName = null,
        public ?string $passScope = null,
        public ?string $passValidDate = null,
        // Restaurant reservation fields
        public ?int $reservationId = null,
        public ?string $diningDate = null,
        public ?string $timeSlot = null,
        public ?int $guestCount = null,
    ) {}
}
