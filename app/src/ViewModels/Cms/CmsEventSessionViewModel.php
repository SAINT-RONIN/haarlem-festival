<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * ViewModel for displaying event sessions in CMS views.
 *
 * All date/time values are pre-formatted and derived properties are pre-calculated
 * so views only need to echo properties.
 */
final readonly class CmsEventSessionViewModel
{
    public function __construct(
        public readonly int     $eventSessionId,
        public readonly int     $eventId,
        public readonly string  $eventTitle,
        public readonly string  $eventTypeSlug,
        public readonly string  $formattedStartTime,
        public readonly string  $formattedEndTime,
        public readonly string  $formattedDate,
        public readonly string  $formattedDateLong,
        public readonly string  $formattedDateTimeLocal,
        public readonly string  $formattedEndDateTimeLocal,
        public readonly int     $capacityTotal,
        public readonly int     $soldSingleTickets,
        public readonly int     $soldReservedSeats,
        public readonly int     $soldTicketsTotal,
        public readonly int     $seatsAvailable,
        public readonly int     $capacitySingleTicketLimit,
        public readonly ?string $languageCode,
        public readonly ?int    $minAge,
        public readonly ?int    $maxAge,
        public readonly bool    $reservationRequired,
        public readonly string  $notes,
        public readonly ?string $historyTicketLabel,
        public readonly ?string $ctaLabel,
        public readonly ?string $ctaUrl,
        public readonly bool    $isActive,
        public readonly ?string $hallName,
        public readonly ?string $sessionType,
        public readonly ?int    $durationMinutes,
        public readonly ?string $ageLabel,
        public readonly bool    $isFree,
        public readonly bool    $isCancelled,
        public readonly string  $sessionDate,
    ) {
    }
}
