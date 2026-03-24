<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the EventSession table.
 *
 * Each session is a specific date/time occurrence of an event — tracks capacity, ticket sales,
 * and scheduling. Sessions are what visitors actually book tickets for.
 */
final readonly class EventSession
{
    public function __construct(
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
    ) {
    }

    /**
     * Creates an EventSession instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            eventSessionId: (int)$row['EventSessionId'],
            eventId: (int)$row['EventId'],
            startDateTime: new \DateTimeImmutable($row['StartDateTime']),
            endDateTime: isset($row['EndDateTime']) ? new \DateTimeImmutable($row['EndDateTime']) : null,
            capacityTotal: (int)$row['CapacityTotal'],
            capacitySingleTicketLimit: (int)$row['CapacitySingleTicketLimit'],
            seatsAvailable: isset($row['SeatsAvailable']) ? (int)$row['SeatsAvailable'] : null,
            soldSingleTickets: (int)$row['SoldSingleTickets'],
            soldReservedSeats: (int)$row['SoldReservedSeats'],
            hallName: $row['HallName'] ?? null,
            sessionType: $row['SessionType'] ?? null,
            durationMinutes: isset($row['DurationMinutes']) ? (int)$row['DurationMinutes'] : null,
            languageCode: $row['LanguageCode'] ?? null,
            minAge: isset($row['MinAge']) ? (int)$row['MinAge'] : null,
            maxAge: isset($row['MaxAge']) ? (int)$row['MaxAge'] : null,
            reservationRequired: (bool)$row['ReservationRequired'],
            isFree: (bool)$row['IsFree'],
            notes: (string)$row['Notes'],
            historyTicketLabel: $row['HistoryTicketLabel'] ?? null,
            ctaLabel: $row['CtaLabel'] ?? null,
            ctaUrl: $row['CtaUrl'] ?? null,
            isCancelled: (bool)$row['IsCancelled'],
            createdAtUtc: new \DateTimeImmutable($row['CreatedAtUtc']),
            isActive: (bool)$row['IsActive'],
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'EventSessionId' => $this->eventSessionId,
            'EventId' => $this->eventId,
            'StartDateTime' => $this->startDateTime->format('Y-m-d H:i:s'),
            'EndDateTime' => $this->endDateTime?->format('Y-m-d H:i:s'),
            'CapacityTotal' => $this->capacityTotal,
            'CapacitySingleTicketLimit' => $this->capacitySingleTicketLimit,
            'SeatsAvailable' => $this->seatsAvailable,
            'SoldSingleTickets' => $this->soldSingleTickets,
            'SoldReservedSeats' => $this->soldReservedSeats,
            'HallName' => $this->hallName,
            'SessionType' => $this->sessionType,
            'DurationMinutes' => $this->durationMinutes,
            'LanguageCode' => $this->languageCode,
            'MinAge' => $this->minAge,
            'MaxAge' => $this->maxAge,
            'ReservationRequired' => $this->reservationRequired,
            'IsFree' => $this->isFree,
            'Notes' => $this->notes,
            'HistoryTicketLabel' => $this->historyTicketLabel,
            'CtaLabel' => $this->ctaLabel,
            'CtaUrl' => $this->ctaUrl,
            'IsCancelled' => $this->isCancelled,
            'CreatedAtUtc' => $this->createdAtUtc->format('Y-m-d H:i:s'),
            'IsActive' => $this->isActive,
        ];
    }
}
