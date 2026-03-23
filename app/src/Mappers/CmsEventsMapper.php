<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Helpers\AgeLabelFormatter;
use App\Helpers\FormatHelper;
use App\Models\EventSession;
use App\Models\EventsListPageData;
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
use App\ViewModels\Cms\CmsSessionPriceViewModel;

/**
 * Transforms event, session, and media-asset domain models into ViewModels
 * consumed by the CMS event-management pages (event list, event edit, media library).
 */
final class CmsEventsMapper
{
    /**
     * Builds the media-library page ViewModel from raw asset data and upload constraints.
     *
     * @return CmsMediaLibraryViewModel Used by the CMS media-library view.
     */
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

    /**
     * Converts a MediaAsset into a plain array suitable for JSON API responses
     * (e.g. the media-picker AJAX endpoint in the CMS).
     */
    public static function toMediaJsonData(MediaAsset $asset): array
    {
        return [
            'mediaAssetId'     => $asset->mediaAssetId,
            'filePath'         => $asset->filePath,
            'originalFileName' => $asset->originalFileName,
            'mimeType'         => $asset->mimeType,
        ];
    }

    /**
     * Converts a MediaAsset into a display-ready list-item ViewModel for the CMS media grid,
     * formatting the file size and creation date for human readability.
     */
    public static function toMediaListItemViewModel(MediaAsset $asset): CmsMediaListItemViewModel
    {
        return new CmsMediaListItemViewModel(
            mediaAssetId: $asset->mediaAssetId,
            filePath: $asset->filePath,
            originalFileName: $asset->originalFileName,
            mimeType: $asset->mimeType,
            fileSize: FormatHelper::fileSize($asset->fileSizeBytes),
            altText: $asset->altText,
            createdAt: $asset->createdAtUtc->format(FormatHelper::CMS_DATE_FORMAT),
        );
    }

    /**
     * Transforms an EventWithDetails domain model into a CMS list-row ViewModel,
     * resolving the active/inactive status badge class and the event-type CSS class.
     */
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
            totalSoldTickets: $event->totalSoldTickets,
            totalCapacity: $event->totalCapacity,
            isActive: $event->isActive,
            typeClass: 'event-type-' . $event->eventTypeSlug,
            statusText: $event->isActive ? 'Active' : 'Inactive',
            statusClass: $event->isActive ? 'badge-success' : 'badge-secondary',
        );
    }

    /**
     * Builds the full event-edit page ViewModel, including session sub-ViewModels and
     * price-tier enrichment. Consumed by the CMS event-edit form.
     */
    public static function toEventEditViewModel(
        EventWithDetails $event,
        array $sessions,
        array $pricesData = [],
        array $labelsData = [],
        ?string $successMessage = null,
        ?string $errorMessage = null,
        array $priceTiers = [],
    ): CmsEventEditViewModel {
        $eventTitle = $event->title;
        $eventTypeSlug = $event->eventTypeSlug;

        $sessionViewModels = array_map(
            static fn(mixed $session): CmsEventSessionViewModel => self::resolveSessionViewModel($session, $eventTitle, $eventTypeSlug),
            $sessions,
        );

        $enrichedPrices = self::enrichPricesWithTierNames($pricesData, $priceTiers);

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
            artistId: $event->artistId,
            restaurantId: $event->restaurantId,
            isActive: $event->isActive,
            sessions: $sessionViewModels,
            sessionPrices: $enrichedPrices,
            sessionLabels: $labelsData,
            successMessage: $successMessage,
            errorMessage: $errorMessage,
        );
    }

    /**
     * Enriches session prices with resolved tier names as typed ViewModels.
     *
     * @return array<int, CmsSessionPriceViewModel[]>
     */
    private static function enrichPricesWithTierNames(array $pricesData, array $priceTiers): array
    {
        $tierNameMap = [];
        foreach ($priceTiers as $tier) {
            $tierNameMap[$tier->priceTierId] = $tier->name;
        }
        $enriched = [];
        foreach ($pricesData as $sessionId => $prices) {
            $enriched[$sessionId] = array_map(
                static fn($price) => new CmsSessionPriceViewModel(
                    priceTierId: $price->priceTierId,
                    tierName: $tierNameMap[$price->priceTierId] ?? 'Unknown',
                    price: $price->price,
                    currencyCode: $price->currencyCode,
                ),
                $prices,
            );
        }
        return $enriched;
    }

    /**
     * Converts an EventSession model into a CMS session ViewModel, computing derived
     * display values: formatted dates/times, sold-ticket totals, available seats, and age label.
     */
    public static function toEventSessionViewModel(
        EventSession $session,
        string $eventTitle = '',
        string $eventTypeSlug = 'default',
    ): CmsEventSessionViewModel {
        $startTimestamp = $session->startDateTime->getTimestamp();
        $endTimestamp = $session->endDateTime?->getTimestamp();
        $ageLabel = AgeLabelFormatter::format($session->minAge, $session->maxAge);
        // Aggregate single + reserved tickets into one total for the CMS dashboard
        $soldTicketsTotal = $session->soldSingleTickets + $session->soldReservedSeats;
        // Fall back to computing availability when the DB column is null
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
            capacitySingleTicketLimit: $session->capacitySingleTicketLimit,
            languageCode: $session->languageCode,
            minAge: $session->minAge,
            maxAge: $session->maxAge,
            reservationRequired: $session->reservationRequired,
            notes: $session->notes,
            historyTicketLabel: $session->historyTicketLabel,
            ctaLabel: $session->ctaLabel,
            ctaUrl: $session->ctaUrl,
            isActive: $session->isActive,
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
        EventsListPageData $pageData,
        string $selectedType,
        string $selectedDay,
        ?string $successMessage,
        ?string $errorMessage,
    ): CmsEventsListViewModel {
        $events = array_map(
            static fn(EventWithDetails $event): CmsEventListItemViewModel => self::toEventListItemViewModel($event),
            $pageData->events,
        );

        return new CmsEventsListViewModel(
            events: $events,
            eventTypes: $pageData->eventTypes,
            venues: $pageData->venues,
            weeklySchedule: self::toWeeklyOverview($pageData->weeklySchedule),
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

    /**
     * Handles both SessionWithEvent and EventSession inputs, adapting whichever
     * type the caller supplies into a unified CmsEventSessionViewModel.
     */
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

    /**
     * Down-converts a SessionWithEvent (joined row) into a plain EventSession so it
     * can be passed to toEventSessionViewModel which expects the simpler type.
     */
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

}
