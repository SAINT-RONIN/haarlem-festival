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
        public int     $eventSessionId,
        public int     $eventId,
        public string  $eventTitle,
        public string  $eventTypeSlug,
        public string  $formattedStartTime,
        public string  $formattedEndTime,
        public string  $formattedDate,
        public string  $formattedDateLong,
        public string  $formattedDateTimeLocal,
        public string  $formattedEndDateTimeLocal,
        public int     $capacityTotal,
        public int     $soldSingleTickets,
        public int     $soldReservedSeats,
        public int     $soldTicketsTotal,
        public int     $seatsAvailable,
        public int     $capacitySingleTicketLimit,
        public ?string $languageCode,
        public ?int    $minAge,
        public ?int    $maxAge,
        public bool    $reservationRequired,
        public string  $notes,
        public ?string $historyTicketLabel,
        public ?string $ctaLabel,
        public ?string $ctaUrl,
        public bool    $isActive,
        public ?string $hallName,
        public ?string $sessionType,
        public ?int    $durationMinutes,
        public ?string $ageLabel,
        public bool    $isFree,
        public bool    $isCancelled,
        public string  $sessionDate,
    ) {
    }
}
