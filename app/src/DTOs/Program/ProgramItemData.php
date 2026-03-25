<?php

declare(strict_types=1);

namespace App\DTOs\Program;

/**
 * A single program item with resolved event/session details for display.
 */
final readonly class ProgramItemData
{
    public function __construct(
        public int $programItemId,
        public int $eventSessionId,
        public int $quantity,
        public float $donationAmount,
        public string $eventTitle,
        public ?string $venueName,
        public ?string $hallName,
        public string $startDateTime,
        public ?string $endDateTime,
        public int $eventTypeId,
        public string $eventTypeName,
        public string $eventTypeSlug,
        public ?string $languageCode,
        public ?int $minAge,
        public ?int $maxAge,
        public bool $isPayWhatYouLike,
        public float $basePrice,
    ) {}
}
