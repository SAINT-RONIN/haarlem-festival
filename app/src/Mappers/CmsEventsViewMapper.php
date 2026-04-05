<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Helpers\AgeLabelFormatter;
use App\Helpers\FormatHelper;
use App\Models\EventSession;
use App\DTOs\Events\EventsListPageData;
use App\Models\EventType;
use App\DTOs\Events\EventWithDetails;
use App\DTOs\Schedule\SessionWithEvent;
use App\ViewModels\Cms\CmsEventCreateViewModel;
use App\ViewModels\Cms\CmsEventEditViewModel;
use App\ViewModels\Cms\CmsEventListItemViewModel;
use App\ViewModels\Cms\CmsEventSessionViewModel;
use App\ViewModels\Cms\CmsEventsListViewModel;
use App\ViewModels\Cms\CmsSessionPriceViewModel;

/**
 * Transforms event and session domain models into ViewModels
 * consumed by the CMS event-management pages (event list, event edit).
 */
final class CmsEventsViewMapper
{
    /**
     * Builds the ViewModel for the event creation page from pre-loaded domain lists.
     */
    public static function toCreateViewModel(
        array $eventTypes,
        array $venues,
        array $artists,
        array $restaurants,
        ?string $errorMessage,
        ?string $successMessage,
        string $preselectedDay,
    ): CmsEventCreateViewModel {
        return new CmsEventCreateViewModel(
            eventTypes: $eventTypes,
            venues: $venues,
            artists: $artists,
            restaurants: $restaurants,
            errorMessage: $errorMessage,
            successMessage: $successMessage,
            preselectedDay: $preselectedDay,
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
        ?string $cmsDetailEditUrl = null,
    ): CmsEventEditViewModel {
        $sessionViewModels = self::buildSessionViewModels($sessions, $event->title, $event->eventTypeSlug);
        $enrichedPrices = self::enrichPricesWithTierNames($pricesData, $priceTiers);

        return self::assembleEditViewModel($event, $sessionViewModels, $enrichedPrices, $labelsData, $cmsDetailEditUrl, $successMessage, $errorMessage);
    }

    /**
     * Converts raw session models into CMS session ViewModels.
     *
     * @return CmsEventSessionViewModel[]
     */
    private static function buildSessionViewModels(array $sessions, string $eventTitle, string $eventTypeSlug): array
    {
        return array_map(
            static fn(mixed $session): CmsEventSessionViewModel => self::resolveSessionViewModel($session, $eventTitle, $eventTypeSlug),
            $sessions,
        );
    }

    /** Assembles the full event-edit ViewModel from prepared sub-data. */
    private static function assembleEditViewModel(
        EventWithDetails $event,
        array $sessionViewModels,
        array $enrichedPrices,
        array $labelsData,
        ?string $cmsDetailEditUrl,
        ?string $successMessage,
        ?string $errorMessage,
    ): CmsEventEditViewModel {
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
            cmsDetailEditUrl: $cmsDetailEditUrl,
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
        $tierNameMap = array_combine(
            array_map(fn($t) => $t->priceTierId, $priceTiers),
            array_map(fn($t) => $t->name, $priceTiers),
        );
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
        $soldTicketsTotal = self::computeSoldTotal($session);
        $seatsAvailable = self::computeAvailableSeats($session, $soldTicketsTotal);

        return new CmsEventSessionViewModel(
            ...self::buildSessionIdentityFields($session, $eventTitle, $eventTypeSlug),
            ...self::buildSessionDateFields($session),
            ...self::buildSessionCapacityFields($session, $soldTicketsTotal, $seatsAvailable),
            ...self::buildSessionMetadataFields($session),
        );
    }

    /** Aggregate single + reserved tickets into one total for the CMS dashboard. */
    private static function computeSoldTotal(EventSession $session): int
    {
        return $session->soldSingleTickets + $session->soldReservedSeats;
    }

    /** Fall back to computing availability when the DB column is null. */
    private static function computeAvailableSeats(EventSession $session, int $soldTotal): int
    {
        return $session->seatsAvailable ?? ($session->capacityTotal - $soldTotal);
    }

    /**
     * @return array<string, mixed>
     */
    private static function buildSessionIdentityFields(EventSession $session, string $eventTitle, string $eventTypeSlug): array
    {
        return [
            'eventSessionId' => $session->eventSessionId,
            'eventId' => $session->eventId,
            'eventTitle' => $eventTitle,
            'eventTypeSlug' => $eventTypeSlug,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function buildSessionDateFields(EventSession $session): array
    {
        $start = $session->startDateTime;
        $end = $session->endDateTime;

        return [
            'formattedStartTime' => $start->format('H:i'),
            'formattedEndTime' => $end?->format('H:i') ?? '',
            'formattedDate' => $start->format('Y-m-d'),
            'formattedDateLong' => $start->format('l, F j, Y'),
            'formattedDateTimeLocal' => $start->format('Y-m-d\TH:i'),
            'formattedEndDateTimeLocal' => $end?->format('Y-m-d\TH:i') ?? '',
            'sessionDate' => $start->format('Y-m-d'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function buildSessionCapacityFields(EventSession $session, int $soldTotal, int $seatsAvailable): array
    {
        return [
            'capacityTotal' => $session->capacityTotal,
            'soldSingleTickets' => $session->soldSingleTickets,
            'soldReservedSeats' => $session->soldReservedSeats,
            'soldTicketsTotal' => $soldTotal,
            'seatsAvailable' => $seatsAvailable,
            'capacitySingleTicketLimit' => $session->capacitySingleTicketLimit,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function buildSessionMetadataFields(EventSession $session): array
    {
        return [
            'languageCode' => $session->languageCode,
            'minAge' => $session->minAge,
            'maxAge' => $session->maxAge,
            'reservationRequired' => $session->reservationRequired,
            'notes' => $session->notes,
            'historyTicketLabel' => $session->historyTicketLabel,
            'ctaLabel' => $session->ctaLabel,
            'ctaUrl' => $session->ctaUrl,
            'isActive' => $session->isActive,
            'hallName' => $session->hallName,
            'sessionType' => $session->sessionType,
            'durationMinutes' => $session->durationMinutes,
            'ageLabel' => AgeLabelFormatter::format($session->minAge, $session->maxAge),
            'isFree' => $session->isFree,
            'isCancelled' => $session->isCancelled,
        ];
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
            typeColorMap: self::buildTypeColorMap(),
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

    /**
     * Returns the Tailwind badge color classes per event type slug for the CMS events list.
     *
     * @return array<string, string>
     */
    private static function buildTypeColorMap(): array
    {
        return [
            'jazz' => 'bg-purple-100 text-purple-800',
            'storytelling' => 'bg-pink-100 text-pink-800',
            'history' => 'bg-amber-100 text-amber-800',
            'dance' => 'bg-blue-100 text-blue-800',
            'restaurant' => 'bg-green-100 text-green-800',
        ];
    }
}
