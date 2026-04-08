<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Schedule;

/**
 * Read-only projection from a JOIN across EventSession, Event, EventType, and Venue.
 *
 * Used by the schedule pages and CMS weekly overview to display sessions with their
 * parent event context.
 */
final readonly class SessionWithEvent
{
    public function __construct(
        // EventSession columns (es.*)
        public int                 $eventSessionId,
        public int                 $eventId,
        public \DateTimeImmutable  $startDateTime,
        public ?\DateTimeImmutable $endDateTime,
        public int                 $capacityTotal,
        public int                 $capacitySingleTicketLimit,
        public ?int                $seatsAvailable,
        public int                 $soldSingleTickets,
        public int                 $soldReservedSeats,
        public ?string             $hallName,
        public ?string             $sessionType,
        public ?int                $durationMinutes,
        public ?string             $languageCode,
        public ?int                $minAge,
        public ?int                $maxAge,
        public bool                $reservationRequired,
        public bool                $isFree,
        public string              $notes,
        public ?string             $historyTicketLabel,
        public ?string             $ctaLabel,
        public ?string             $ctaUrl,
        public bool                $isCancelled,
        public \DateTimeImmutable  $createdAtUtc,
        public bool                $isActive,
        // Computed / joined columns
        public string              $sessionDate,
        public string              $dayOfWeek,
        public string              $eventTitle,
        public string              $eventSlug,
        public int                 $eventTypeId,
        public string              $eventTypeName,
        public string              $eventTypeSlug,
        public ?string             $venueName,
        public ?string             $artistName,
        public ?string             $artistImageUrl,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            eventSessionId: (int) $row['EventSessionId'],
            eventId: (int) $row['EventId'],
            startDateTime: new \DateTimeImmutable($row['StartDateTime']),
            endDateTime: isset($row['EndDateTime']) ? new \DateTimeImmutable($row['EndDateTime']) : null,
            capacityTotal: (int) $row['CapacityTotal'],
            capacitySingleTicketLimit: (int) $row['CapacitySingleTicketLimit'],
            seatsAvailable: isset($row['SeatsAvailable']) ? (int) $row['SeatsAvailable'] : null,
            soldSingleTickets: (int) $row['SoldSingleTickets'],
            soldReservedSeats: (int) $row['SoldReservedSeats'],
            hallName: $row['HallName'] ?? null,
            sessionType: $row['SessionType'] ?? null,
            durationMinutes: isset($row['DurationMinutes']) ? (int) $row['DurationMinutes'] : null,
            languageCode: $row['LanguageCode'] ?? null,
            minAge: isset($row['MinAge']) && $row['MinAge'] !== null ? (int) $row['MinAge'] : null,
            maxAge: isset($row['MaxAge']) && $row['MaxAge'] !== null ? (int) $row['MaxAge'] : null,
            reservationRequired: (bool) $row['ReservationRequired'],
            isFree: (bool) $row['IsFree'],
            notes: (string) ($row['Notes'] ?? ''),
            historyTicketLabel: $row['HistoryTicketLabel'] ?? null,
            ctaLabel: $row['CtaLabel'] ?? null,
            ctaUrl: $row['CtaUrl'] ?? null,
            isCancelled: (bool) $row['IsCancelled'],
            createdAtUtc: new \DateTimeImmutable($row['CreatedAtUtc']),
            isActive: (bool) $row['IsActive'],
            sessionDate: (string) $row['SessionDate'],
            dayOfWeek: (string) $row['DayOfWeek'],
            eventTitle: (string) ($row['EventTitle'] ?? ''),
            eventSlug: (string) ($row['EventSlug'] ?? ''),
            eventTypeId: (int) $row['EventTypeId'],
            eventTypeName: (string) ($row['EventTypeName'] ?? ''),
            eventTypeSlug: (string) ($row['EventTypeSlug'] ?? ''),
            venueName: $row['VenueName'] ?? null,
            artistName: $row['ArtistName'] ?? null,
            artistImageUrl: $row['ArtistImageUrl'] ?? null,
        );
    }
}
