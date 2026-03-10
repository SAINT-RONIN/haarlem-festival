<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

use App\Models\EventSession;
use App\ViewModels\Age\AgeLabelFormatter;

/**
 * ViewModel for displaying event sessions in CMS views.
 *
 * Pre-formats all date/time values and calculates derived properties
 * so views only need to echo properties.
 */
class CmsEventSessionViewModel
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
        public readonly ?string $hallName,
        public readonly ?string $sessionType,
        public readonly ?int    $durationMinutes,
        public readonly ?string $ageLabel,
        public readonly bool    $isFree,
        public readonly bool    $isCancelled,
        public readonly string  $sessionDate,
    ) {
    }

    /**
     * Creates a ViewModel from a session data array.
     *
     * @param array{
     *     EventSessionId: int,
     *     EventId: int,
     *     EventTitle?: string,
     *     EventTypeSlug?: string,
     *     StartDateTime: string,
     *     EndDateTime: ?string,
     *     CapacityTotal: int,
     *     SoldSingleTickets: int,
     *     SoldReservedSeats: int,
     *     HallName: ?string,
     *     SessionType: ?string,
     *     DurationMinutes: ?int,
     *     MinAge?: ?int,
     *     MaxAge?: ?int,
     *     IsFree?: int|bool,
     *     IsCancelled?: int|bool
     * } $data
     */
    public static function fromArray(array $data): self
    {
        $startDateTime = $data['StartDateTime'];
        $endDateTime = $data['EndDateTime'] ?? null;
        $startTimestamp = strtotime($startDateTime);
        $endTimestamp = $endDateTime ? strtotime($endDateTime) : null;

        $capacityTotal = (int)$data['CapacityTotal'];
        $soldSingleTickets = (int)$data['SoldSingleTickets'];
        $soldReservedSeats = (int)$data['SoldReservedSeats'];
        $minAge = isset($data['MinAge']) ? (int)$data['MinAge'] : null;
        $maxAge = isset($data['MaxAge']) ? (int)$data['MaxAge'] : null;
        $ageLabel = AgeLabelFormatter::format($minAge, $maxAge);

        return new self(
            eventSessionId: (int)$data['EventSessionId'],
            eventId: (int)$data['EventId'],
            eventTitle: (string)($data['EventTitle'] ?? ''),
            eventTypeSlug: (string)($data['EventTypeSlug'] ?? 'default'),
            formattedStartTime: date('H:i', $startTimestamp),
            formattedEndTime: $endTimestamp ? date('H:i', $endTimestamp) : '',
            formattedDate: date('Y-m-d', $startTimestamp),
            formattedDateLong: date('l, F j, Y', $startTimestamp),
            formattedDateTimeLocal: date('Y-m-d\TH:i', $startTimestamp),
            formattedEndDateTimeLocal: $endTimestamp ? date('Y-m-d\TH:i', $endTimestamp) : '',
            capacityTotal: $capacityTotal,
            soldSingleTickets: $soldSingleTickets,
            soldReservedSeats: $soldReservedSeats,
            soldTicketsTotal: $soldSingleTickets + $soldReservedSeats,
            seatsAvailable: $capacityTotal - $soldSingleTickets - $soldReservedSeats,
            hallName: $data['HallName'] ?? null,
            sessionType: $data['SessionType'] ?? null,
            durationMinutes: isset($data['DurationMinutes']) ? (int)$data['DurationMinutes'] : null,
            ageLabel: $ageLabel,
            isFree: (bool)($data['IsFree'] ?? false),
            isCancelled: (bool)($data['IsCancelled'] ?? false),
            sessionDate: date('Y-m-d', $startTimestamp),
        );
    }

    /**
     * Creates a ViewModel from an EventSession model.
     *
     * @param EventSession $session The EventSession model
     * @param string $eventTitle Optional event title for display
     * @param string $eventTypeSlug Optional event type slug for styling
     */
    public static function fromEventSession(
        EventSession $session,
        string       $eventTitle = '',
        string       $eventTypeSlug = 'default'
    ): self {
        $startTimestamp = $session->startDateTime->getTimestamp();
        $endTimestamp = $session->endDateTime?->getTimestamp();
        $ageLabel = AgeLabelFormatter::format($session->minAge, $session->maxAge);

        $soldTicketsTotal = $session->soldSingleTickets + $session->soldReservedSeats;
        $seatsAvailable = $session->seatsAvailable ?? ($session->capacityTotal - $soldTicketsTotal);

        return new self(
            eventSessionId: $session->eventSessionId,
            eventId: $session->eventId,
            eventTitle: $eventTitle,
            eventTypeSlug: $eventTypeSlug,
            formattedStartTime: date('H:i', $startTimestamp),
            formattedEndTime: $endTimestamp ? date('H:i', $endTimestamp) : '',
            formattedDate: date('Y-m-d', $startTimestamp),
            formattedDateLong: date('l, F j, Y', $startTimestamp),
            formattedDateTimeLocal: date('Y-m-d\TH:i', $startTimestamp),
            formattedEndDateTimeLocal: $endTimestamp ? date('Y-m-d\TH:i', $endTimestamp) : '',
            capacityTotal: $session->capacityTotal,
            soldSingleTickets: $session->soldSingleTickets,
            soldReservedSeats: $session->soldReservedSeats,
            soldTicketsTotal: $soldTicketsTotal,
            seatsAvailable: $seatsAvailable,
            hallName: $session->hallName,
            sessionType: $session->sessionType,
            durationMinutes: $session->durationMinutes,
            ageLabel: $ageLabel,
            isFree: $session->isFree,
            isCancelled: $session->isCancelled,
            sessionDate: date('Y-m-d', $startTimestamp),
        );
    }
}
