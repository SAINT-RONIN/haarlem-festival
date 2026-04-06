<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * Typed carrier for CMS event-session create/update form fields.
 */
final readonly class EventSessionUpsertData
{
    public function __construct(
        public int $eventId,
        public string $startDateTime,
        public string $endDateTime,
        public ?int $capacityTotal = null,
        public ?int $capacitySingleTicketLimit = null,
        public ?string $hallName = null,
        public ?string $sessionType = null,
        public ?int $durationMinutes = null,
        public ?string $languageCode = null,
        public ?int $minAge = null,
        public ?int $maxAge = null,
        public bool $reservationRequired = false,
        public bool $isFree = false,
        public string $notes = '',
        public ?string $historyTicketLabel = null,
        public ?string $ctaLabel = null,
        public ?string $ctaUrl = null,
        public bool $isCancelled = false,
        public bool $isActive = true,
    ) {
    }
}
