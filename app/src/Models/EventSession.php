<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `EventSession` SQL table.
 *
 * Columns verified against complete-database-11-02-2026.sql.
 */
class EventSession
{
    public function __construct(
        public readonly int                 $eventSessionId,
        public readonly int                 $eventId,
        public readonly \DateTimeImmutable  $startDateTime,
        public readonly ?\DateTimeImmutable $endDateTime,
        public readonly int                 $capacityTotal,
        public readonly int                 $capacitySingleTicketLimit,
        public readonly ?int                $seatsAvailable,
        public readonly int                 $soldSingleTickets,
        public readonly int                 $soldReservedSeats,
        public readonly ?string             $hallName,
        public readonly ?string             $sessionType,
        public readonly ?int                $durationMinutes,
        public readonly ?string             $languageCode,
        public readonly ?int                $minAge,
        public readonly ?int                $maxAge,
        public readonly bool                $reservationRequired,
        public readonly bool                $isFree,
        public readonly string              $notes,
        public readonly ?string             $historyTicketLabel,
        public readonly ?string             $ctaLabel,
        public readonly ?string             $ctaUrl,
        public readonly bool                $isCancelled,
        public readonly \DateTimeImmutable  $createdAtUtc,
        public readonly bool                $isActive,
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
