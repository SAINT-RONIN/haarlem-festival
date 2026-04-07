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

    /**
     * Returns a copy of this DTO with the event ID set to the given value.
     *
     * Used on the create path where the event ID comes from the route parameter,
     * not from the form — the form may not post an EventId field at all.
     */
    public function forEvent(int $eventId): self
    {
        return new self(
            eventId:                   $eventId,
            startDateTime:             $this->startDateTime,
            endDateTime:               $this->endDateTime,
            capacityTotal:             $this->capacityTotal,
            capacitySingleTicketLimit: $this->capacitySingleTicketLimit,
            hallName:                  $this->hallName,
            sessionType:               $this->sessionType,
            durationMinutes:           $this->durationMinutes,
            languageCode:              $this->languageCode,
            minAge:                    $this->minAge,
            maxAge:                    $this->maxAge,
            reservationRequired:       $this->reservationRequired,
            isFree:                    $this->isFree,
            notes:                     $this->notes,
            historyTicketLabel:        $this->historyTicketLabel,
            ctaLabel:                  $this->ctaLabel,
            ctaUrl:                    $this->ctaUrl,
            isCancelled:               $this->isCancelled,
            isActive:                  $this->isActive,
        );
    }
}
