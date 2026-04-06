<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EventTypeId;
use App\Enums\PriceTierId;
use App\Helpers\FormatHelper;
use App\Helpers\HistorySessionHelper;
use App\Helpers\SessionGroupingHelper;
use App\DTOs\Filters\EventSessionFilter;
use App\DTOs\Schedule\ScheduleSectionData;
use App\DTOs\Schedule\SessionWithEvent;
use App\Models\EventSessionLabel;
use App\Models\EventSessionPrice;
use App\DTOs\Filters\EventTypeFilter;
use App\DTOs\Filters\ScheduleFilterParams;
use App\Content\ScheduleSectionContent;
use App\DTOs\Schedule\SessionQueryResult;
use App\Repositories\Interfaces\IScheduleContentRepository;
use App\Repositories\Interfaces\IEventSessionLabelRepository;
use App\Repositories\Interfaces\IEventSessionPriceRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IEventTypeRepository;
use App\Services\Interfaces\IScheduleDayVisibilityResolver;
use App\Exceptions\PageLoadException;
use App\Services\Interfaces\IScheduleService;
use App\Helpers\AgeLabelFormatter;

/**
 * Service for building schedule sections for any event type.
 *
 * This is a global service - not tied to any specific event type.
 */
class ScheduleService implements IScheduleService
{
    public function __construct(
        private readonly IScheduleContentRepository $scheduleContentRepo,
        private readonly IEventSessionRepository $sessionRepository,
        private readonly IEventSessionLabelRepository $labelRepository,
        private readonly IEventSessionPriceRepository $priceRepository,
        private readonly IEventTypeRepository $eventTypeRepository,
        private readonly IScheduleDayVisibilityResolver $visibilityResolver,
    ) {
    }

    /**
     * Builds the complete schedule payload for a given event type.
     *
     * Loads CMS labels, resolves visible days from global + type-specific
     * day configs, fetches sessions with optional filters (day, time, price,
     * venue, language, age), batch-loads labels and prices, then assembles
     * day-grouped event card arrays ready for the mapper layer.
     *
     */
    /**
     * @throws PageLoadException When an unexpected error occurs while building schedule data
     */
    public function getScheduleData(
        string $pageSlug,
        int $eventTypeId,
        int $maxDays = 4,
        ?int $eventId = null,
        ?string $ctaTextOverride = null,
        ?ScheduleFilterParams $filterParams = null,
    ): ScheduleSectionData {
        try {
            return $this->assembleScheduleData($pageSlug, $eventTypeId, $maxDays, $eventId, $ctaTextOverride, $filterParams);
        } catch (\Throwable $error) {
            throw new PageLoadException('Failed to load schedule data.', 0, $error);
        }
    }

    /** Converts a day name (e.g. "Monday") to a MySQL DAYOFWEEK number (1=Sunday..7=Saturday). */
    private function convertDayNameToNumber(?string $dayName): ?int
    {
        if ($dayName === null || $dayName === '') {
            return null;
        }

        return FormatHelper::dayNameToMysqlDayOfWeek($dayName);
    }

    /** Fetches all data sources and assembles the schedule payload. */
    private function assembleScheduleData(
        string $pageSlug,
        int $eventTypeId,
        int $maxDays,
        ?int $eventId,
        ?string $ctaTextOverride,
        ?ScheduleFilterParams $filterParams,
    ): ScheduleSectionData {
        $eventTypeSlug = $this->resolveEventTypeSlug($eventTypeId, $pageSlug);
        $cmsSection = $this->scheduleContentRepo->findScheduleSectionContent($pageSlug, 'schedule_section');
        $visibleDays = $this->getVisibleDays($eventTypeId);

        $availableDays = $this->fetchAvailableDays($eventTypeId, $visibleDays, $eventId, $maxDays);
        $scheduleData = $this->fetchFilteredSessions($eventTypeId, $visibleDays, $eventId, $maxDays, $filterParams);
        $displayStrings = $this->resolveCmsDisplayStrings($cmsSection, $ctaTextOverride);

        $days = $this->buildScheduleDays($scheduleData, $eventTypeSlug, $eventTypeId, $displayStrings);

        return new ScheduleSectionData(
            cmsContent: $cmsSection,
            pageSlug: $pageSlug,
            eventTypeSlug: $eventTypeSlug,
            eventTypeId: $eventTypeId,
            days: $days,
            activeFilters: $filterParams,
            availableDays: $availableDays,
            filterGroupTypes: $this->resolveFilterGroupTypes($eventTypeSlug),
            priceTypeOptions: $this->resolvePriceTypeOptions($eventTypeSlug),
        );
    }

    /** Resolves the event type slug used for URLs and filter logic. */
    private function resolveEventTypeSlug(int $eventTypeId, string $fallback): string
    {
        $eventType = $this->eventTypeRepository->findEventTypes(new EventTypeFilter(eventTypeId: $eventTypeId))[0] ?? null;
        return $eventType?->slug ?? $fallback;
    }

    /** Fetches distinct calendar days that have matching sessions (for day-tab navigation). */
    private function fetchAvailableDays(int $eventTypeId, array $visibleDays, ?int $eventId, int $maxDays): array
    {
        return $this->sessionRepository->findDistinctDays(
            new EventSessionFilter(
                eventTypeId: $eventTypeId,
                isActive: true,
                eventIsActive: true,
                includeCancelled: false,
                visibleDays: $visibleDays,
                eventId: $eventId,
                maxDays: $maxDays,
            ),
        );
    }

    /** Fetches sessions applying all user-selected filters. */
    private function fetchFilteredSessions(
        int $eventTypeId,
        array $visibleDays,
        ?int $eventId,
        int $maxDays,
        ?ScheduleFilterParams $filterParams,
    ): SessionQueryResult {
        return $this->sessionRepository->findSessions(
            new EventSessionFilter(
                eventTypeId: $eventTypeId,
                isActive: true,
                eventIsActive: true,
                includeCancelled: false,
                groupByDay: true,
                maxDays: $maxDays,
                visibleDays: $visibleDays,
                orderBy: 'es.StartDateTime ASC',
                eventId: $eventId,
                dayOfWeekNumber: $this->convertDayNameToNumber($filterParams?->day),
                timeRange: $filterParams?->timeRange,
                priceType: $filterParams?->priceType,
                venueName: $filterParams?->venue,
                languageCode: $filterParams?->language,
                filterMinAge: $filterParams?->age,
                startTime: $filterParams?->startTime,
                limit: 50,
            ),
        );
    }

    /**
     * Resolves CMS display strings with hardcoded fallbacks.
     *
     * @return array{ctaButtonText: string, payWhatYouLikeText: string, currencySymbol: string, startPoint: string, groupTicketFallback: string}
     */
    private function resolveCmsDisplayStrings(ScheduleSectionContent $cmsSection, ?string $ctaTextOverride): array
    {
        return [
            'ctaButtonText' => $ctaTextOverride ?? ($cmsSection->scheduleCtaButtonText ?? 'Discover'),
            'payWhatYouLikeText' => $cmsSection->schedulePayWhatYouLikeText ?? 'Pay as you like',
            'currencySymbol' => $cmsSection->scheduleCurrencySymbol ?? '€',
            'startPoint' => $cmsSection->scheduleStartPoint ?? 'A giant flag near Church of St. Bavo at Grote Markt',
            'groupTicketFallback' => $cmsSection->scheduleHistoryGroupTicket ?? 'Group ticket- best value for 4 people',
        ];
    }

    /** Builds day ViewModels from schedule data with batch-loaded labels and prices. */
    private function buildScheduleDays(
        SessionQueryResult $scheduleData,
        string $eventTypeSlug,
        int $eventTypeId,
        array $displayStrings,
    ): array {
        if (empty($scheduleData->days)) {
            return [];
        }

        $sessionIds = $this->collectSessionIds($scheduleData->sessions);
        $labelsMap = $this->batchLoadLabels($sessionIds);
        $pricesMap = $this->batchLoadPrices($sessionIds);
        $sessionsByDate = SessionGroupingHelper::groupByDate($scheduleData->sessions);

        return $this->buildDayArrays($scheduleData->days, $sessionsByDate, $eventTypeSlug, $eventTypeId, $labelsMap, $pricesMap, $displayStrings);
    }

    /**
     * Extracts session IDs from a list of sessions.
     *
     * @param \App\DTOs\Schedule\SessionWithEvent[] $sessions
     * @return int[]
     */
    private function collectSessionIds(array $sessions): array
    {
        return array_map(static fn($s) => $s->eventSessionId, $sessions);
    }

    /** Iterates days and builds event card arrays for each. */
    private function buildDayArrays(
        array $days,
        array $sessionsByDate,
        string $eventTypeSlug,
        int $eventTypeId,
        array &$labelsMap,
        array $pricesMap,
        array $displayStrings,
    ): array {
        $dayArrays = [];
        foreach ($days as $day) {
            $dayArrays[] = $this->buildSingleDay($day, $sessionsByDate, $eventTypeSlug, $eventTypeId, $labelsMap, $pricesMap, $displayStrings);
        }
        return $dayArrays;
    }

    /**
     * Batch-loads session labels for all session IDs.
     *
     * @param int[] $sessionIds
     * @return array<int, EventSessionLabel[]>
     */
    private function batchLoadLabels(array $sessionIds): array
    {
        return !empty($sessionIds)
            ? $this->labelRepository->findLabelsBySessionIds($sessionIds)
            : [];
    }

    /**
     * Batch-loads session prices for all session IDs.
     *
     * @param int[] $sessionIds
     * @return array<int, EventSessionPrice[]>
     */
    private function batchLoadPrices(array $sessionIds): array
    {
        return !empty($sessionIds)
            ? $this->priceRepository->findPricesBySessionIds($sessionIds)
            : [];
    }

    /**
     * Assembles a single day's event cards, applying History time-slot grouping if needed.
     *
     * For History tours, sessions at the same time slot (one per language) are first merged
     * into a single session via groupSessionsByTimeSlot before building cards. For all other
     * event types, sessions are used as-is.
     *
     * @param object                          $day            Day object with a `date` property ("Y-m-d").
     * @param array<string, SessionWithEvent[]> $sessionsByDate Sessions indexed by ISO date string.
     * @param string                          $eventTypeSlug  URL slug for the event type (e.g. "history").
     * @param int                             $eventTypeId    Numeric event type ID from EventTypeId enum.
     * @param array                           $labelsMap      Labels indexed by session ID (passed by reference — History grouping may add merged labels).
     * @param array<int, EventSessionPrice[]> $pricesMap      Prices indexed by session ID.
     * @param array                           $displayStrings Translated UI strings for card rendering.
     * @return array{dayName: string, isoDate: string, events: array, isEmpty: bool}
     */
    private function buildSingleDay(
        object $day,
        array $sessionsByDate,
        string $eventTypeSlug,
        int $eventTypeId,
        array &$labelsMap,
        array $pricesMap,
        array $displayStrings,
    ): array {
        $date        = $day->date;
        $daySessions = $sessionsByDate[$date] ?? [];
        $historyTourOptionsMap = [];

        if ($eventTypeId === EventTypeId::History->value) {
            [$daySessions, $labelsMap, $historyTourOptionsMap] = $this->groupSessionsByTimeSlot($daySessions, $labelsMap, $pricesMap);
        }

        $events = $this->buildEventCardsForDay($daySessions, $eventTypeSlug, $eventTypeId, $labelsMap, $pricesMap, $displayStrings, $historyTourOptionsMap);

        return [
            'dayName' => (new \DateTimeImmutable($date))->format('l'),
            'isoDate' => $date,
            'events'  => $events,
            'isEmpty' => empty($events),
        ];
    }

    /**
     * Builds an array of event card arrays for a single day's sessions.
     *
     * Iterates each session and delegates to buildEventCard for the per-session shape.
     * History tour booking options are looked up by session ID from $historyTourOptionsMap
     * (empty array for non-History event types).
     *
     * @param SessionWithEvent[]                               $daySessions          Sessions to render.
     * @param string                                           $eventTypeSlug        URL slug for card links.
     * @param int                                              $eventTypeId          Numeric event type ID.
     * @param array                                            $labelsMap            Labels indexed by session ID.
     * @param array<int, EventSessionPrice[]>                  $pricesMap            Prices indexed by session ID.
     * @param array                                            $displayStrings       Translated UI strings.
     * @param array<int, array<int, array<string, mixed>>>     $historyTourOptionsMap Booking options for History tours, keyed by session ID.
     * @return array[]
     */
    private function buildEventCardsForDay(
        array $daySessions,
        string $eventTypeSlug,
        int $eventTypeId,
        array $labelsMap,
        array $pricesMap,
        array $displayStrings,
        array $historyTourOptionsMap,
    ): array {
        $events = [];

        foreach ($daySessions as $session) {
            $events[] = $this->buildEventCard(
                $session,
                $eventTypeSlug,
                $eventTypeId,
                $labelsMap,
                $pricesMap,
                $displayStrings,
                $historyTourOptionsMap[$session->eventSessionId] ?? [],
            );
        }

        return $events;
    }

    /**
     * Groups History tour sessions by StartDateTime, merging language labels.
     *
     * Multiple sessions at the same time slot (one per language) are merged
     * into a single session with combined labels for the schedule card.
     *
     * @param SessionWithEvent[] $sessions Sessions for a single day
     * @param array $labelsMap Pre-loaded labels indexed by session ID
     * @param array<int, EventSessionPrice[]> $pricesMap
     * @return array{0: SessionWithEvent[], 1: array, 2: array<int, array<int, array<string, mixed>>>}
     */
    private function groupSessionsByTimeSlot(array $sessions, array $labelsMap, array $pricesMap): array
    {
        $grouped = [];
        $groupedSessionsByTime = [];
        foreach ($sessions as $session) {
            $timeKey = $session->startDateTime->format('Y-m-d H:i:s');
            if (!isset($grouped[$timeKey])) {
                $grouped[$timeKey] = $session;
                $groupedSessionsByTime[$timeKey] = [$session];
            } else {
                $groupedSessionsByTime[$timeKey][] = $session;
                $labelsMap = $this->mergeSessionLabels($labelsMap, $grouped[$timeKey]->eventSessionId, $session->eventSessionId);
            }
        }

        $historyTourOptionsMap = [];
        foreach ($grouped as $timeKey => $primarySession) {
            $historyTourOptionsMap[$primarySession->eventSessionId] = $this->buildHistoryTourOptions(
                $groupedSessionsByTime[$timeKey] ?? [$primarySession],
                $labelsMap,
                $pricesMap,
            );
        }

        return [array_values($grouped), $labelsMap, $historyTourOptionsMap];
    }

    /** Merges unique labels from a secondary session into the primary session's label list. */
    private function mergeSessionLabels(array $labelsMap, int $primaryId, int $sourceId): array
    {
        $sourceLabels = $labelsMap[$sourceId] ?? [];
        if (!isset($labelsMap[$primaryId])) {
            $labelsMap[$primaryId] = [];
        }

        $existingTexts = array_map(fn(EventSessionLabel $l) => $l->labelText, $labelsMap[$primaryId]);

        foreach ($sourceLabels as $label) {
            if (!in_array($label->labelText, $existingTexts, true)) {
                $labelsMap[$primaryId][] = $label;
                $existingTexts[] = $label->labelText;
            }
        }

        return $labelsMap;
    }

    /**
     * Assembles a single event card array by resolving age range, labels,
     * pricing, and CTA for one session.
     *
     * @return array<string, mixed>
     */
    private function buildEventCard(
        \App\DTOs\Schedule\SessionWithEvent $session,
        string $eventTypeSlug,
        int $eventTypeId,
        array $labelsMap,
        array $pricesMap,
        array $displayStrings,
        array $historyTourOptions = [],
    ): array {
        $sessionId = $session->eventSessionId;

        [$minAge, $maxAge] = $this->resolveAgeRange($session);
        $labels = $this->extractLabels($labelsMap[$sessionId] ?? [], $minAge, $maxAge, $eventTypeId);
        $priceData = $this->resolvePrice($pricesMap[$sessionId] ?? []);
        $cta = $this->resolveCta($session, $eventTypeSlug, $displayStrings['ctaButtonText']);

        return $this->buildCardArray($session, $eventTypeSlug, $eventTypeId, $labels, $minAge, $maxAge, $priceData, $cta, $displayStrings, $historyTourOptions);
    }

    /**
     * Extracts and normalises min/max age from a session, swapping if inverted.
     *
     * @return array{0: ?int, 1: ?int}
     */
    private function resolveAgeRange(\App\DTOs\Schedule\SessionWithEvent $session): array
    {
        $minAge = $session->minAge !== null && $session->minAge > 0 ? $session->minAge : null;
        $maxAge = $session->maxAge !== null && $session->maxAge > 0 ? $session->maxAge : null;

        if ($minAge !== null && $maxAge !== null && $minAge > $maxAge) {
            [$minAge, $maxAge] = [$maxAge, $minAge];
        }

        return [$minAge, $maxAge];
    }

    /**
     * Converts label models to plain strings and appends an age-range label
     * (except for History events, which handle age labels differently).
     *
     * @param EventSessionLabel[] $sessionLabels
     * @return string[]
     */
    private function extractLabels(array $sessionLabels, ?int $minAge, ?int $maxAge, int $eventTypeId): array
    {
        $labels = array_map(fn (EventSessionLabel $l) => $l->labelText, $sessionLabels);

        if ($eventTypeId === EventTypeId::History->value) {
            return $labels;
        }

        return AgeLabelFormatter::appendToLabels($labels, $minAge, $maxAge);
    }

    /**
     * Resolves the call-to-action label and URL, falling back to a
     * convention-based URL (/{eventTypeSlug}/{eventSlug}) when none is set.
     *
     * @return array{label: string, url: string}
     */
    private function resolveCta(\App\DTOs\Schedule\SessionWithEvent $session, string $eventTypeSlug, string $defaultCtaText): array
    {
        $label = !empty($session->ctaLabel) ? $session->ctaLabel : $defaultCtaText;
        $url = !empty($session->ctaUrl) ? $session->ctaUrl : '/' . $eventTypeSlug . '/' . $session->eventSlug;
        return ['label' => $label, 'url' => $url];
    }

    /**
     * Assembles the final associative array representing one event card in the schedule grid.
     *
     * @return array<string, mixed>
     */
    private function buildCardArray(
        \App\DTOs\Schedule\SessionWithEvent $session,
        string $eventTypeSlug,
        int $eventTypeId,
        array $labels,
        ?int $minAge,
        ?int $maxAge,
        array $priceData,
        array $cta,
        array $displayStrings,
        array $historyTourOptions = [],
    ): array {
        return array_merge(
            $this->buildCardIdentityFields($session, $eventTypeSlug, $eventTypeId),
            $this->buildCardPriceFields($priceData, $cta, $displayStrings),
            $this->buildCardLocationFields($session, $displayStrings['startPoint']),
            $this->buildCardTimeFields($session),
            $this->buildCardDetailFields($session, $labels, $minAge, $maxAge, $priceData, $displayStrings['groupTicketFallback'], $historyTourOptions),
        );
    }

    /** Core identity fields for the event card. */
    private function buildCardIdentityFields(\App\DTOs\Schedule\SessionWithEvent $session, string $eventTypeSlug, int $eventTypeId): array
    {
        return [
            'eventSessionId' => $session->eventSessionId,
            'eventId' => $session->eventId,
            'eventTypeSlug' => $eventTypeSlug,
            'eventTypeId' => $eventTypeId,
            'title' => $session->eventTitle,
            'isHistory' => ($eventTypeId === EventTypeId::History->value),
        ];
    }

    /** Pricing and CTA fields for the event card. */
    private function buildCardPriceFields(array $priceData, array $cta, array $displayStrings): array
    {
        return [
            'priceAmount' => $priceData['amount'],
            'isPayWhatYouLike' => $priceData['isPayWhatYouLike'],
            'payWhatYouLikeText' => $displayStrings['payWhatYouLikeText'],
            'currencySymbol' => $displayStrings['currencySymbol'],
            'ctaLabel' => $cta['label'],
            'ctaUrl' => $cta['url'],
            'priceType' => $this->resolvePriceType($priceData),
        ];
    }

    /** Location fields for the event card. */
    private function buildCardLocationFields(\App\DTOs\Schedule\SessionWithEvent $session, string $startPoint): array
    {
        return [
            'locationName' => $session->venueName ?? $startPoint,
            'hallName' => $session->hallName ?? '',
            'venueName' => $session->venueName ?? '',
        ];
    }

    /** DateTime fields for the event card. */
    private function buildCardTimeFields(\App\DTOs\Schedule\SessionWithEvent $session): array
    {
        return [
            'startDateTime' => $session->startDateTime,
            'endDateTime' => $session->endDateTime,
            'isoDate' => $session->startDateTime->format('Y-m-d'),
            'startTimeIso' => $session->startDateTime->format('H:i'),
            'endTimeIso' => $session->endDateTime ? $session->endDateTime->format('H:i') : '',
            'timeRange' => $this->computeTimeRange($session->startDateTime),
        ];
    }

    /** Remaining detail fields for the event card: labels, capacity, age, artist, history. */
    private function buildCardDetailFields(
        \App\DTOs\Schedule\SessionWithEvent $session,
        array $labels,
        ?int $minAge,
        ?int $maxAge,
        array $priceData,
        string $groupTicketFallback,
        array $historyTourOptions = [],
    ): array {
        return [
            'labels' => $labels,
            'capacityTotal' => $session->capacityTotal,
            'seatsAvailable' => $session->seatsAvailable,
            'minAge' => $minAge,
            'maxAge' => $maxAge,
            'ageLabel' => AgeLabelFormatter::format($minAge, $maxAge),
            'artistName' => $session->artistName,
            'artistImageUrl' => $session->artistImageUrl,
            'historyTicketLabel' => $session->historyTicketLabel ?? $groupTicketFallback ?: null,
            'historyTourOptions' => $historyTourOptions,
        ];
    }

    /**
     * @param SessionWithEvent[] $sessions
     * @param array<int, EventSessionLabel[]> $labelsMap
     * @param array<int, EventSessionPrice[]> $pricesMap
     * @return array<int, array<string, mixed>>
     */
    private function buildHistoryTourOptions(array $sessions, array $labelsMap, array $pricesMap): array
    {
        $options = [];
        $seenLanguageKeys = [];
        $sharedPrices = $this->buildSharedHistoryPrices($sessions, $pricesMap);

        foreach ($sessions as $session) {
            $labels = $labelsMap[$session->eventSessionId] ?? [];
            $languageLabel = HistorySessionHelper::resolveLanguageLabel($session->languageCode, $labels);
            $languageKey = HistorySessionHelper::resolveLanguageKey($session->languageCode, $labels);

            if ($languageLabel === null || $languageLabel === '') {
                continue;
            }

            if ($languageKey !== null && isset($seenLanguageKeys[$languageKey])) {
                continue;
            }

            if ($languageKey !== null) {
                $seenLanguageKeys[$languageKey] = true;
            }

            $options[$session->eventSessionId] = [
                'language' => $languageLabel,
                'seatsAvailable' => HistorySessionHelper::resolveSeatsAvailable($session),
                'prices' => $sharedPrices,
            ];
        }

        return $options;
    }

    /**
     * @param SessionWithEvent[] $sessions
     * @param array<int, EventSessionPrice[]> $pricesMap
     * @return array<int, array{priceTierId: int, price: string}>
     */
    private function buildSharedHistoryPrices(array $sessions, array $pricesMap): array
    {
        $sharedPrices = [];

        foreach ($sessions as $session) {
            $sharedPrices = HistorySessionHelper::mergeHighestPricesByKey(
                $sharedPrices,
                $pricesMap[$session->eventSessionId] ?? [],
            );
        }

        return array_values(array_map(
            static fn(EventSessionPrice $price): array => [
                'priceTierId' => $price->priceTierId,
                'price' => $price->price,
            ],
            $sharedPrices,
        ));
    }

    /** Maps price data to a human-readable price type string. */
    private function resolvePriceType(array $priceData): string
    {
        if ($priceData['isPayWhatYouLike']) {
            return 'pay-what-you-like';
        }

        return ($priceData['amount'] === null || $priceData['amount'] == 0) ? 'free' : 'fixed';
    }

    /**
     * Maps a start time to a human-readable time-of-day bucket for filtering.
     */
    private function computeTimeRange(\DateTimeInterface $startDateTime): string
    {
        $hour = (int) $startDateTime->format('G');
        if ($hour < 12) {
            return 'morning';
        }
        return $hour < 17 ? 'afternoon' : 'evening';
    }

    /**
     * Picks the best price from the session prices list.
     * Returns the raw amount (float or null) and whether it is pay-what-you-like.
     *
     * @param EventSessionPrice[] $prices
     * @return array{amount: float|null, isPayWhatYouLike: bool}
     */
    private function resolvePrice(array $prices): array
    {
        // Priority 1: pay-what-you-like takes precedence over all fixed prices
        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::PayWhatYouLike->value) {
                return ['amount' => null, 'isPayWhatYouLike' => true];
            }
        }

        // Priority 2: prefer the Adult tier as the display price
        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::Adult->value) {
                return ['amount' => (float)$price->price, 'isPayWhatYouLike' => false];
            }
        }

        // Fallback: use whatever the first price tier is
        if (!empty($prices)) {
            return ['amount' => (float)$prices[0]->price, 'isPayWhatYouLike' => false];
        }

        return ['amount' => null, 'isPayWhatYouLike' => false];
    }

    /**
     * Returns the filter categories shown in the UI for a given event type
     * (e.g. storytelling shows day/time/price/language/age, jazz shows day/venue/price).
     *
     * @return string[]
     */
    private function resolveFilterGroupTypes(string $eventTypeSlug): array
    {
        return match ($eventTypeSlug) {
            'storytelling' => ['day', 'timeRange', 'priceType', 'language', 'ageGroup'],
            'jazz'         => ['day', 'venue', 'priceType'],
            'history'      => ['day', 'startTime'],
            default        => ['day'],
        };
    }

    /**
     * Returns the allowed price-type filter options for a given event type.
     * Jazz events don't support pay-what-you-like, so that option is excluded.
     *
     * @return string[]
     */
    private function resolvePriceTypeOptions(string $eventTypeSlug): array
    {
        return $eventTypeSlug === 'jazz'
            ? ['free', 'fixed']
            : ['pay-what-you-like', 'fixed'];
    }

    private function getVisibleDays(int $eventTypeId): array
    {
        return $this->visibilityResolver->getVisibleDays($eventTypeId);
    }
}
