<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\HistoryPageConstants;
use App\Constants\JazzPageConstants;
use App\Constants\ScheduleConstants;
use App\Constants\StorytellingPageConstants;
use App\Enums\EventTypeId;
use App\Helpers\FormatHelper;
use App\Helpers\HistorySessionHelper;
use App\Helpers\SessionGroupingHelper;
use App\DTOs\Cms\ScheduleSectionContent;
use App\DTOs\Domain\Filters\EventSessionFilter;
use App\DTOs\Domain\Filters\EventTypeFilter;
use App\DTOs\Domain\Filters\ScheduleFilterParams;
use App\DTOs\Domain\Schedule\ScheduleDisplayStrings;
use App\DTOs\Domain\Schedule\ScheduleSectionData;
use App\DTOs\Domain\Schedule\SessionQueryResult;
use App\DTOs\Domain\Schedule\SessionWithEvent;
use App\Exceptions\PageLoadException;
use App\Mappers\ScheduleCardMapper;
use App\Models\EventSessionLabel;
use App\Models\EventSessionPrice;
use App\Repositories\Interfaces\IEventSessionLabelRepository;
use App\Repositories\Interfaces\IEventSessionPriceRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IEventTypeRepository;
use App\Repositories\Interfaces\IScheduleContentRepository;
use App\Services\Interfaces\IScheduleDayVisibilityResolver;
use App\Services\Interfaces\IScheduleService;

/** Builds schedule section payloads for any event type. */
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

    /** @throws PageLoadException When an unexpected error occurs while building schedule data */
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

    private function assembleScheduleData(
        string $pageSlug,
        int $eventTypeId,
        int $maxDays,
        ?int $eventId,
        ?string $ctaTextOverride,
        ?ScheduleFilterParams $filterParams,
    ): ScheduleSectionData {
        $eventTypeSlug  = $this->resolveEventTypeSlug($eventTypeId, $pageSlug);
        $cmsSection     = $this->scheduleContentRepo->findScheduleSectionContent($pageSlug, ScheduleConstants::SCHEDULE_SECTION_KEY);
        $visibleDays    = $this->getVisibleDays($eventTypeId);
        $availableDays  = $this->fetchAvailableDays($eventTypeId, $visibleDays, $eventId, $maxDays);
        $scheduleData   = $this->fetchFilteredSessions($eventTypeId, $visibleDays, $eventId, $maxDays, $filterParams);
        $displayStrings = $this->resolveCmsDisplayStrings($cmsSection, $ctaTextOverride);
        $days           = $this->buildScheduleDays($scheduleData, $eventTypeSlug, $eventTypeId, $displayStrings);

        return new ScheduleSectionData(
            cmsContent:       $cmsSection,
            pageSlug:         $pageSlug,
            eventTypeSlug:    $eventTypeSlug,
            eventTypeId:      $eventTypeId,
            days:             $days,
            activeFilters:    $filterParams,
            availableDays:    $availableDays,
            filterGroupTypes: $this->resolveFilterGroupTypes($eventTypeSlug),
            priceTypeOptions: $this->resolvePriceTypeOptions($eventTypeSlug),
        );
    }

    private function resolveEventTypeSlug(int $eventTypeId, string $fallback): string
    {
        $eventType = $this->eventTypeRepository->findEventTypes(new EventTypeFilter(eventTypeId: $eventTypeId))[0] ?? null;
        return $eventType?->slug ?? $fallback;
    }

    /** Fetches distinct calendar days that have at least one matching session (used for day-tab navigation). */
    private function fetchAvailableDays(int $eventTypeId, array $visibleDays, ?int $eventId, int $maxDays): array
    {
        return $this->sessionRepository->findDistinctDays(
            new EventSessionFilter(
                eventTypeId:      $eventTypeId,
                isActive:         true,
                eventIsActive:    true,
                includeCancelled: false,
                visibleDays:      $visibleDays,
                eventId:          $eventId,
                maxDays:          $maxDays,
            ),
        );
    }

    private function fetchFilteredSessions(
        int $eventTypeId,
        array $visibleDays,
        ?int $eventId,
        int $maxDays,
        ?ScheduleFilterParams $filterParams,
    ): SessionQueryResult {
        return $this->sessionRepository->findSessions(
            new EventSessionFilter(
                eventTypeId:      $eventTypeId,
                isActive:         true,
                eventIsActive:    true,
                includeCancelled: false,
                groupByDay:       true,
                maxDays:          $maxDays,
                visibleDays:      $visibleDays,
                orderBy:          ScheduleConstants::ORDER_BY_START_DATETIME,
                eventId:          $eventId,
                dayOfWeekNumber:  $this->convertDayNameToNumber($filterParams?->day),
                timeRange:        $filterParams?->timeRange,
                priceType:        $filterParams?->priceType,
                venueName:        $filterParams?->venue,
                languageCode:     $filterParams?->language,
                filterMinAge:     $filterParams?->age,
                startTime:        $filterParams?->startTime,
                limit:            50,
            ),
        );
    }

    private function resolveCmsDisplayStrings(ScheduleSectionContent $cmsSection, ?string $ctaTextOverride): ScheduleDisplayStrings
    {
        return new ScheduleDisplayStrings(
            ctaButtonText:       $ctaTextOverride ?? ($cmsSection->scheduleCtaButtonText ?? ScheduleConstants::DEFAULT_CTA_BUTTON_TEXT),
            payWhatYouLikeText:  $cmsSection->schedulePayWhatYouLikeText ?? ScheduleConstants::DEFAULT_PAY_WHAT_YOU_LIKE_TEXT,
            currencySymbol:      $cmsSection->scheduleCurrencySymbol ?? ScheduleConstants::DEFAULT_CURRENCY_SYMBOL,
            startPoint:          $cmsSection->scheduleStartPoint ?? ScheduleConstants::DEFAULT_HISTORY_START_POINT,
            groupTicketFallback: $cmsSection->scheduleHistoryGroupTicket ?? ScheduleConstants::DEFAULT_HISTORY_GROUP_TICKET,
        );
    }

    private function buildScheduleDays(
        SessionQueryResult $scheduleData,
        string $eventTypeSlug,
        int $eventTypeId,
        ScheduleDisplayStrings $displayStrings,
    ): array {
        if (empty($scheduleData->days)) {
            return [];
        }

        $sessionIds     = $this->collectSessionIds($scheduleData->sessions);
        $labelsMap      = $this->batchLoadLabels($sessionIds);
        $pricesMap      = $this->batchLoadPrices($sessionIds);
        $sessionsByDate = SessionGroupingHelper::groupByDate($scheduleData->sessions);

        return $this->buildDayArrays($scheduleData->days, $sessionsByDate, $eventTypeSlug, $eventTypeId, $labelsMap, $pricesMap, $displayStrings);
    }

    /**
     * @param SessionWithEvent[] $sessions
     * @return int[]
     */
    private function collectSessionIds(array $sessions): array
    {
        return array_map(static fn($s) => $s->eventSessionId, $sessions);
    }

    private function buildDayArrays(
        array $days,
        array $sessionsByDate,
        string $eventTypeSlug,
        int $eventTypeId,
        array &$labelsMap,
        array $pricesMap,
        ScheduleDisplayStrings $displayStrings,
    ): array {
        $dayArrays = [];
        foreach ($days as $day) {
            $dayArrays[] = $this->buildSingleDay($day, $sessionsByDate, $eventTypeSlug, $eventTypeId, $labelsMap, $pricesMap, $displayStrings);
        }
        return $dayArrays;
    }

    /**
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
     * For History tours, sessions at the same time slot (one per language) are first merged
     * into a single session via groupSessionsByTimeSlot. $labelsMap is passed by reference
     * because History grouping may append merged language labels to it.
     *
     * @return array{dayName: string, isoDate: string, events: array, isEmpty: bool}
     */
    private function buildSingleDay(
        object $day,
        array $sessionsByDate,
        string $eventTypeSlug,
        int $eventTypeId,
        array &$labelsMap,
        array $pricesMap,
        ScheduleDisplayStrings $displayStrings,
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

    /** @return array[] */
    private function buildEventCardsForDay(
        array $daySessions,
        string $eventTypeSlug,
        int $eventTypeId,
        array $labelsMap,
        array $pricesMap,
        ScheduleDisplayStrings $displayStrings,
        array $historyTourOptionsMap,
    ): array {
        $events = [];

        foreach ($daySessions as $session) {
            $events[] = ScheduleCardMapper::buildEventCardArray(
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
     * Multiple sessions at the same time slot (one per language) become one card with combined labels.
     *
     * @param SessionWithEvent[] $sessions
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
            $labels        = $labelsMap[$session->eventSessionId] ?? [];
            $languageLabel = HistorySessionHelper::resolveLanguageLabel($session->languageCode, $labels);
            $languageKey   = HistorySessionHelper::resolveLanguageKey($session->languageCode, $labels);

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
                'language'       => $languageLabel,
                'seatsAvailable' => HistorySessionHelper::resolveSeatsAvailable($session),
                'prices'         => $sharedPrices,
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
                'price'       => $price->price,
            ],
            $sharedPrices,
        ));
    }

    /**
     * Returns the filter categories shown in the schedule UI for a given event type.
     *
     * @return string[]
     */
    private function resolveFilterGroupTypes(string $eventTypeSlug): array
    {
        return match ($eventTypeSlug) {
            StorytellingPageConstants::PAGE_SLUG => [
                ScheduleConstants::FILTER_DAY,
                ScheduleConstants::FILTER_TIME_RANGE,
                ScheduleConstants::FILTER_PRICE_TYPE,
                ScheduleConstants::FILTER_LANGUAGE,
                ScheduleConstants::FILTER_AGE_GROUP,
            ],
            JazzPageConstants::PAGE_SLUG => [
                ScheduleConstants::FILTER_DAY,
                ScheduleConstants::FILTER_VENUE,
                ScheduleConstants::FILTER_PRICE_TYPE,
            ],
            HistoryPageConstants::PAGE_SLUG => [
                ScheduleConstants::FILTER_DAY,
                ScheduleConstants::FILTER_START_TIME,
            ],
            default => [ScheduleConstants::FILTER_DAY],
        };
    }

    /**
     * Jazz events don't support pay-what-you-like, so that option is excluded for jazz.
     *
     * @return string[]
     */
    private function resolvePriceTypeOptions(string $eventTypeSlug): array
    {
        return $eventTypeSlug === JazzPageConstants::PAGE_SLUG
            ? [ScheduleConstants::PRICE_TYPE_FREE, ScheduleConstants::PRICE_TYPE_FIXED]
            : [ScheduleConstants::PRICE_TYPE_PAY_WHAT_YOU_LIKE, ScheduleConstants::PRICE_TYPE_FIXED];
    }

    private function getVisibleDays(int $eventTypeId): array
    {
        return $this->visibilityResolver->getVisibleDays($eventTypeId);
    }
}
