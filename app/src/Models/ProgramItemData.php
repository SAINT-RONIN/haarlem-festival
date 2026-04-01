<?php

declare(strict_types=1);

namespace App\Models;

final readonly class ProgramItemData
{
    public function __construct(
        public int     $programItemId,
        public ?int    $eventSessionId,
        public int     $quantity,
        public float   $donationAmount,
        public string  $eventTitle,
        public ?string $venueName,
        public ?string $hallName,
        public string  $startDateTime,
        public ?string $endDateTime,
        public int     $eventTypeId,
        public string  $eventTypeName,
        public string  $eventTypeSlug,
        public ?string $languageCode,
        public ?int    $minAge,
        public ?int    $maxAge,
        public bool    $isPayWhatYouLike,
        public float   $basePrice,
        // Restaurant-reservation-specific fields (null for non-restaurant items)
        public ?int    $reservationId = null,
        public ?string $diningDate = null,
        public ?string $timeSlot = null,
        public ?int    $guestCount = null,
    ) {}
}
