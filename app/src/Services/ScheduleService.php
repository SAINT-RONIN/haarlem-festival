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
use App\DTOs\Domain\Schedule\ScheduleDayPayload;
use App\Exceptions\PageLoadException;
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
    ) {}

    /** @throws PageLoadException */
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

        $sessionIds     = $this->collectSessionIds($scheduleData->sessions);
        $labelsMap      = $this->batchLoadLabels($sessionIds);
        $pricesMap      = $this->batchLoadPrices($sessionIds);
        $sessionsByDate = SessionGroupingHelper::groupByDate($scheduleData->sessions);
        $days           = empty($scheduleData->days)
            ? []
            : $this->buildDayPayloads($scheduleData->days, $sessionsByDate, $eventTypeId, $labelsMap, $pricesMap);

        return new ScheduleSectionData(
            cmsContent: $cmsSection,
            pageSlug: $pageSlug,
            eventTypeSlug: $eventTypeSlug,
            eventTypeId: $eventTypeId,
            days: $days,
            displayStrings: $displayStrings,
            pricesMap: $pricesMap,
            activeFilters: $filterParams,
            availableDays: $availableDays,
            filterGroupTypes: $this->resolveFilterGroupTypes($eventTypeSlug),
            priceTypeOptions: $this->resolvePriceTypeOptions($eventTypeSlug),
        );
    }

    private function resolveEventTypeSlug(int $eventTypeId, string $fallback): string
    {
        $eventType = $this->eventTypeRepository->findEventTypes(new EventTypeFilter(eventTypeId: $eventTypeId))[0] ?? null;
        return $eventType?->slug ?? $fallback;
    }

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
                orderBy: ScheduleConstants::ORDER_BY_START_DATETIME,
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

    private function resolveCmsDisplayStrings(ScheduleSectionContent $cmsSection, ?string $ctaTextOverride): ScheduleDisplayStrings
    {
        return new ScheduleDisplayStrings(
            ctaButtonText: $ctaTextOverride ?? ($cmsSection->scheduleCtaButtonText ?? ScheduleConstants::DEFAULT_CTA_BUTTON_TEXT),
            payWhatYouLikeText: $cmsSection->schedulePayWhatYouLikeText ?? ScheduleConstants::DEFAULT_PAY_WHAT_YOU_LIKE_TEXT,
            currencySymbol: $cmsSection->scheduleCurrencySymbol ?? ScheduleConstants::DEFAULT_CURRENCY_SYMBOL,
            startPoint: $cmsSection->scheduleStartPoint ?? ScheduleConstants::DEFAULT_HISTORY_START_POINT,
            groupTicketFallback: $cmsSection->scheduleHistoryGroupTicket ?? ScheduleConstants::DEFAULT_HISTORY_GROUP_TICKET,
        );
    }

    /** @param SessionWithEvent[] $sessions @return int[] */
    private function collectSessionIds(array $sessions): array
    {
        return array_map(static fn($s) => $s->eventSessionId, $sessions);
    }

    /** @return ScheduleDayPayload[] */
    private function buildDayPayloads(
        array $days,
        array $sessionsByDate,
        int $eventTypeId,
        array &$labelsMap,
        array $pricesMap,
    ): array {
        $payloads = [];
        foreach ($days as $day) {
            $payloads[] = $this->buildSingleDay($day, $sessionsByDate, $eventTypeId, $labelsMap, $pricesMap);
        }
        return $payloads;
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

    // History tours: sessions at the same time slot are merged via groupSessionsByTimeSlot.
    // $labelsMap is passed by reference because grouping may append merged language labels.
    private function buildSingleDay(
        object $day,
        array $sessionsByDate,
        int $eventTypeId,
        array &$labelsMap,
        array $pricesMap,
    ): ScheduleDayPayload {
        $date        = $day->date;
        $daySessions = $sessionsByDate[$date] ?? [];
        $historyTourOptions = [];

        if ($eventTypeId === EventTypeId::History->value) {
            [$daySessions, $labelsMap, $historyTourOptions] = $this->groupSessionsByTimeSlot($daySessions, $labelsMap, $pricesMap);
        }

        return new ScheduleDayPayload(
            dayName: new \DateTimeImmutable($date)->format('l'),
            isoDate: $date,
            isEmpty: empty($daySessions),
            sessions: $daySessions,
            labelsMap: $labelsMap,
            historyTourOptions: $historyTourOptions,
        );
    }

    /** @param SessionWithEvent[] $sessions @return array{0: SessionWithEvent[], 1: array, 2: array<int, array<int, array<string, mixed>>>} */
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

    /** @return string[] */
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

    // Jazz doesn't support pay-what-you-like, so that option is excluded.
    /** @return string[] */
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
