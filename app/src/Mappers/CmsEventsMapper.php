<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Helpers\AgeLabelFormatter;
use App\Models\EventSession;
use App\Models\EventType;
use App\Models\EventWithDetails;
use App\Models\MediaAsset;
use App\Models\SessionWithEvent;
use App\Models\Venue;
use App\ViewModels\Cms\CmsEventEditViewModel;
use App\ViewModels\Cms\CmsEventListItemViewModel;
use App\ViewModels\Cms\CmsEventSessionViewModel;
use App\ViewModels\Cms\CmsEventsListViewModel;
use App\ViewModels\Cms\CmsMediaLibraryViewModel;
use App\ViewModels\Cms\CmsMediaListItemViewModel;

class CmsEventsMapper
{
    public static function toMediaLibraryViewModel(
        array $assets,
        array $imageLimits,
        string $csrfToken,
        ?string $successMessage,
        ?string $errorMessage
    ): CmsMediaLibraryViewModel {
        return new CmsMediaLibraryViewModel(
            assets: $assets,
            imageLimits: $imageLimits,
            csrfToken: $csrfToken,
            successMessage: $successMessage,
            errorMessage: $errorMessage,
        );
    }

    public static function toMediaJsonData(MediaAsset $asset): array
    {
        return [
            'mediaAssetId'     => $asset->mediaAssetId,
            'filePath'         => $asset->filePath,
            'originalFileName' => $asset->originalFileName,
            'mimeType'         => $asset->mimeType,
        ];
    }

    public static function toMediaListItemViewModel(MediaAsset $asset): CmsMediaListItemViewModel
    {
        return new CmsMediaListItemViewModel(
            mediaAssetId: $asset->mediaAssetId,
            filePath: $asset->filePath,
            originalFileName: $asset->originalFileName,
            mimeType: $asset->mimeType,
            fileSize: self::formatFileSize($asset->fileSizeBytes),
            altText: $asset->altText,
            createdAt: $asset->createdAtUtc->format('d M Y, H:i'),
        );
    }

    public static function toEventListItemViewModel(EventWithDetails $event): CmsEventListItemViewModel
    {
        return new CmsEventListItemViewModel(
            eventId: $event->eventId,
            title: $event->title,
            shortDescription: $event->shortDescription,
            eventTypeId: $event->eventTypeId,
            eventTypeName: $event->eventTypeName,
            eventTypeSlug: $event->eventTypeSlug,
            venueName: $event->venueName,
            sessionCount: $event->sessionCount,
            isActive: $event->isActive,
            typeClass: 'event-type-' . $event->eventTypeSlug,
            statusText: $event->isActive ? 'Active' : 'Inactive',
            statusClass: $event->isActive ? 'badge-success' : 'badge-secondary',
        );
    }

    public static function toEventEditViewModel(
        EventWithDetails $event,
        array $sessions,
        array $pricesData = [],
        array $labelsData = [],
    ): CmsEventEditViewModel {
        $eventTitle = $event->title;
        $eventTypeSlug = $event->eventTypeSlug;

        $sessionViewModels = array_map(
            static fn(mixed $session): CmsEventSessionViewModel => self::resolveSessionViewModel($session, $eventTitle, $eventTypeSlug),
            $sessions,
        );

        return new CmsEventEditViewModel(
            eventId: $event->eventId,
            title: $event->title,
            shortDescription: $event->shortDescription,
            longDescriptionHtml: $event->longDescriptionHtml !== '' ? $event->longDescriptionHtml : '<p></p>',
            eventTypeId: $event->eventTypeId,
            eventTypeName: $event->eventTypeName,
            eventTypeSlug: $event->eventTypeSlug,
            venueId: $event->venueId,
            venueName: $event->venueName,
            isActive: $event->isActive,
            sessions: $sessionViewModels,
            sessionPrices: $pricesData,
            sessionLabels: $labelsData,
        );
    }

    public static function toEventSessionViewModel(
        EventSession $session,
        string $eventTitle = '',
        string $eventTypeSlug = 'default',
    ): CmsEventSessionViewModel {
        $startTimestamp = $session->startDateTime->getTimestamp();
        $endTimestamp = $session->endDateTime?->getTimestamp();
        $ageLabel = AgeLabelFormatter::format($session->minAge, $session->maxAge);
        $soldTicketsTotal = $session->soldSingleTickets + $session->soldReservedSeats;
        $seatsAvailable = $session->seatsAvailable ?? ($session->capacityTotal - $soldTicketsTotal);

        return new CmsEventSessionViewModel(
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

    /**
     * Builds a CmsEventsListViewModel from raw domain data.
     *
     * @param EventWithDetails[] $eventsData
     * @param EventType[]        $eventTypes
     * @param Venue[]            $venues
     * @param array<string, SessionWithEvent[]> $weeklyScheduleDomain
     */
    public static function toEventsListViewModel(
        array $eventsData,
        array $eventTypes,
        array $venues,
        array $weeklyScheduleDomain,
        string $selectedType,
        string $selectedDay,
        ?string $successMessage,
        ?string $errorMessage,
    ): CmsEventsListViewModel {
        $events = array_map(
            static fn(EventWithDetails $event): CmsEventListItemViewModel => self::toEventListItemViewModel($event),
            $eventsData,
        );

        return new CmsEventsListViewModel(
            events: $events,
            eventTypes: $eventTypes,
            venues: $venues,
            weeklySchedule: self::toWeeklyOverview($weeklyScheduleDomain),
            selectedType: $selectedType,
            selectedDay: $selectedDay,
            successMessage: $successMessage,
            errorMessage: $errorMessage,
        );
    }

    /**
     * Groups SessionWithEvent models by day name into CmsEventSessionViewModel arrays.
     *
     * @param array<string, SessionWithEvent[]> $sessionsByDay
     * @return array<string, CmsEventSessionViewModel[]>
     */
    public static function toWeeklyOverview(array $sessionsByDay): array
    {
        $result = [];
        foreach ($sessionsByDay as $dayName => $sessions) {
            $result[$dayName] = array_map(
                static fn(SessionWithEvent $s): CmsEventSessionViewModel => self::toEventSessionViewModel(
                    self::toEventSession($s),
                    $s->eventTitle,
                    $s->eventTypeSlug,
                ),
                $sessions,
            );
        }
        return $result;
    }

    private static function resolveSessionViewModel(mixed $session, string $eventTitle, string $eventTypeSlug): CmsEventSessionViewModel
    {
        if ($session instanceof SessionWithEvent) {
            return self::toEventSessionViewModel(
                self::toEventSession($session),
                $session->eventTitle ?: $eventTitle,
                $session->eventTypeSlug ?: $eventTypeSlug,
            );
        }

        return self::toEventSessionViewModel($session, $eventTitle, $eventTypeSlug);
    }

    private static function toEventSession(SessionWithEvent $s): EventSession
    {
        return new EventSession(
            eventSessionId:            $s->eventSessionId,
            eventId:                   $s->eventId,
            startDateTime:             $s->startDateTime,
            endDateTime:               $s->endDateTime,
            capacityTotal:             $s->capacityTotal,
            capacitySingleTicketLimit: $s->capacitySingleTicketLimit,
            seatsAvailable:            $s->seatsAvailable,
            soldSingleTickets:         $s->soldSingleTickets,
            soldReservedSeats:         $s->soldReservedSeats,
            hallName:                  $s->hallName,
            sessionType:               $s->sessionType,
            durationMinutes:           $s->durationMinutes,
            languageCode:              $s->languageCode,
            minAge:                    $s->minAge,
            maxAge:                    $s->maxAge,
            reservationRequired:       $s->reservationRequired,
            isFree:                    $s->isFree,
            notes:                     $s->notes,
            historyTicketLabel:        $s->historyTicketLabel,
            ctaLabel:                  $s->ctaLabel,
            ctaUrl:                    $s->ctaUrl,
            isCancelled:               $s->isCancelled,
            createdAtUtc:              $s->createdAtUtc,
            isActive:                  $s->isActive,
        );
    }

    private static function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        return round($bytes / 1024, 1) . ' KB';
    }
}
