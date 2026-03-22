<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DayOfWeek;
use App\Enums\EventTypeId;
use App\Enums\PriceTierId;
use App\Models\EventSessionFilter;
use App\Models\EventSessionLabel;
use App\Models\EventSessionPrice;
use App\Models\EventTypeFilter;
use App\Models\ScheduleDayConfigFilter;
use App\Models\ScheduleFilterParams;
use App\Models\ScheduleSectionContent;
use App\Models\SessionQueryResult;
use App\Repositories\Interfaces\ICmsContentRepository;
use App\Repositories\Interfaces\IEventSessionLabelRepository;
use App\Repositories\Interfaces\IEventSessionPriceRepository;
use App\Repositories\Interfaces\IEventSessionRepository;
use App\Repositories\Interfaces\IEventTypeRepository;
use App\Repositories\Interfaces\IScheduleDayConfigRepository;
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
        private ICmsContentRepository $cmsService,
        private IEventSessionRepository $sessionRepository,
        private IEventSessionLabelRepository $labelRepository,
        private IEventSessionPriceRepository $priceRepository,
        private IEventTypeRepository $eventTypeRepository,
        private IScheduleDayConfigRepository $scheduleDayConfigRepository,
    ) {
    }

    public function getScheduleData(
        string $pageSlug,
        int $eventTypeId,
        int $maxDays = 4,
        ?int $eventId = null,
        ?string $ctaTextOverride = null,
        ?ScheduleFilterParams $filterParams = null,
    ): array {
        $eventType = $this->eventTypeRepository->findEventTypes(new EventTypeFilter(eventTypeId: $eventTypeId))[0] ?? null;
        $eventTypeSlug = $eventType?->slug ?? $pageSlug;

        $cmsRaw = $this->cmsService->getSectionContent($pageSlug, 'schedule_section');
        $cmsSection = ScheduleSectionContent::fromRawArray($cmsRaw);
        $visibleDays = $this->getVisibleDays($eventTypeId);

        $availableDays = $this->sessionRepository->findDistinctDays(
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

        $scheduleData = $this->sessionRepository->findSessions(
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
                dayOfWeek: $filterParams?->day,
                timeRange: $filterParams?->timeRange,
                priceType: $filterParams?->priceType,
                venueName: $filterParams?->venue,
                languageCode: $filterParams?->language,
                filterMinAge: $filterParams?->age,
                limit: 50,
            ),
        );

        $ctaButtonText = $ctaTextOverride ?? ($cmsSection->scheduleCtaButtonText ?? 'Discover');
        $payWhatYouLikeText = $cmsSection->schedulePayWhatYouLikeText ?? 'Pay as you like';
        $currencySymbol = $cmsSection->scheduleCurrencySymbol ?? '€';
        $startPoint = $cmsSection->scheduleStartPoint ?? '';
        $groupTicketFallback = $cmsSection->scheduleHistoryGroupTicket ?? '';

        $days = $this->buildScheduleDays(
            $scheduleData,
            $eventTypeSlug,
            $eventTypeId,
            $ctaButtonText,
            $payWhatYouLikeText,
            $currencySymbol,
            $startPoint,
            $groupTicketFallback
        );

        return [
            'cmsContent' => $cmsRaw,
            'pageSlug' => $pageSlug,
            'eventTypeSlug' => $eventTypeSlug,
            'eventTypeId' => $eventTypeId,
            'days' => $days,
            'activeFilters' => $filterParams,
            'availableDays' => $availableDays,
            'filterGroupTypes' => $this->resolveFilterGroupTypes($eventTypeSlug),
            'priceTypeOptions' => $this->resolvePriceTypeOptions($eventTypeSlug),
        ];
    }

    /**
     * Builds day ViewModels from schedule data.
     */
    private function buildScheduleDays(
        SessionQueryResult $scheduleData,
        string             $eventTypeSlug,
        int                $eventTypeId,
        string             $defaultCtaText,
        string             $payWhatYouLikeText,
        string             $currencySymbol,
        string             $startPoint = '',
        string             $groupTicketFallback = ''
    ): array {
        $days = $scheduleData->days;
        $sessions = $scheduleData->sessions;

        if (empty($days)) {
            return [];
        }

        // Get session IDs for batch loading labels and prices
        $sessionIds = array_map(static fn ($s) => $s->eventSessionId, $sessions);
        $labelsMap = !empty($sessionIds)
            ? $this->labelRepository->findLabelsBySessionIds($sessionIds)
            : [];
        $pricesMap = !empty($sessionIds)
            ? $this->priceRepository->findPricesBySessionIds($sessionIds)
            : [];

        // Group sessions by date
        $sessionsByDate = [];
        foreach ($sessions as $session) {
            $date = $session->sessionDate;
            if (!isset($sessionsByDate[$date])) {
                $sessionsByDate[$date] = [];
            }
            $sessionsByDate[$date][] = $session;
        }

        $dayArrays = [];
        foreach ($days as $day) {
            $date = $day->date;
            $dateObj = new \DateTimeImmutable($date);
            $daySessions = $sessionsByDate[$date] ?? [];

            $events = [];

            // For History, group sessions by time slot and merge language labels
            if ($eventTypeId === EventTypeId::History->value) {
                [$daySessions, $labelsMap] = $this->groupSessionsByTimeSlot($daySessions, $labelsMap);
            }

            foreach ($daySessions as $session) {
                $events[] = $this->buildEventCard(
                    $session,
                    $eventTypeSlug,
                    $eventTypeId,
                    $labelsMap,
                    $pricesMap,
                    $defaultCtaText,
                    $payWhatYouLikeText,
                    $currencySymbol,
                    $startPoint,
                    $groupTicketFallback
                );
            }

            $dayArrays[] = [
                'dayName'  => $dateObj->format('l'),
                'isoDate'  => $date,
                'events'   => $events,
                'isEmpty'  => empty($events),
            ];
        }

        return $dayArrays;
    }

    /**
     * Groups History tour sessions by StartDateTime, merging language labels.
     *
     * Multiple sessions at the same time slot (one per language) are merged
     * into a single session with combined labels for the schedule card.
     *
     * @param array $sessions Sessions for a single day
     * @param array $labelsMap Pre-loaded labels indexed by session ID
     * @return array{0: array, 1: array} [groupedSessions, updatedLabelsMap]
     */
    private function groupSessionsByTimeSlot(array $sessions, array $labelsMap): array
    {
        $grouped = [];
        foreach ($sessions as $session) {
            $timeKey = $session->startDateTime->format('Y-m-d H:i:s');
            if (!isset($grouped[$timeKey])) {
                $grouped[$timeKey] = $session;
            } else {
                // Merge labels from this session into the primary session's labels
                $primaryId = $grouped[$timeKey]->eventSessionId;
                $currentId = $session->eventSessionId;
                $currentLabels = $labelsMap[$currentId] ?? [];
                if (!isset($labelsMap[$primaryId])) {
                    $labelsMap[$primaryId] = [];
                }
                $existingTexts = array_map(
                    fn(EventSessionLabel $l) => $l->labelText,
                    $labelsMap[$primaryId]
                );
                foreach ($currentLabels as $label) {
                    if (!in_array($label->labelText, $existingTexts, true)) {
                        $labelsMap[$primaryId][] = $label;
                        $existingTexts[] = $label->labelText;
                    }
                }
            }
        }
        return [array_values($grouped), $labelsMap];
    }

    /**
     * Builds a plain event card array from session data.
     *
     * @return array<string, mixed>
     */
    private function buildEventCard(
        \App\Models\SessionWithEvent $session,
        string $eventTypeSlug,
        int    $eventTypeId,
        array  $labelsMap,
        array  $pricesMap,
        string $defaultCtaText,
        string $payWhatYouLikeText,
        string $currencySymbol,
        string $startPoint = '',
        string $groupTicketFallback = ''
    ): array {
        $sessionId = $session->eventSessionId;
        [$minAge, $maxAge] = $this->resolveAgeRange($session);
        $labels = $this->extractLabels($labelsMap[$sessionId] ?? [], $minAge, $maxAge);
        $priceData = $this->resolvePrice($pricesMap[$sessionId] ?? []);
        $cta = $this->resolveCta($session, $eventTypeSlug, $defaultCtaText);

        return $this->buildCardArray($session, $eventTypeSlug, $eventTypeId, $labels, $minAge, $maxAge, $priceData, $cta, $payWhatYouLikeText, $currencySymbol, $startPoint, $groupTicketFallback);
    }

    /**
     * @return array{0: ?int, 1: ?int}
     */
    private function resolveAgeRange(\App\Models\SessionWithEvent $session): array
    {
        $minAge = $session->minAge !== null && $session->minAge > 0 ? $session->minAge : null;
        $maxAge = $session->maxAge !== null && $session->maxAge > 0 ? $session->maxAge : null;

        if ($minAge !== null && $maxAge !== null && $minAge > $maxAge) {
            [$minAge, $maxAge] = [$maxAge, $minAge];
        }

        return [$minAge, $maxAge];
    }

    /**
     * @param EventSessionLabel[] $sessionLabels
     * @return string[]
     */
    private function extractLabels(array $sessionLabels, ?int $minAge, ?int $maxAge): array
    {
        $labels = array_map(fn (EventSessionLabel $l) => $l->labelText, $sessionLabels);
        return AgeLabelFormatter::appendToLabels($labels, $minAge, $maxAge);
    }

    /**
     * @return array{label: string, url: string}
     */
    private function resolveCta(\App\Models\SessionWithEvent $session, string $eventTypeSlug, string $defaultCtaText): array
    {
        $label = !empty($session->ctaLabel) ? $session->ctaLabel : $defaultCtaText;
        $url = !empty($session->ctaUrl) ? $session->ctaUrl : '/' . $eventTypeSlug . '/' . $session->eventSlug;
        return ['label' => $label, 'url' => $url];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildCardArray(
        \App\Models\SessionWithEvent $session,
        string $eventTypeSlug,
        int $eventTypeId,
        array $labels,
        ?int $minAge,
        ?int $maxAge,
        array $priceData,
        array $cta,
        string $payWhatYouLikeText,
        string $currencySymbol,
        string $startPoint = '',
        string $groupTicketFallback = ''
    ): array {
        $startDateTime = $session->startDateTime;
        $endDateTime = $session->endDateTime;

        return [
            'eventSessionId' => $session->eventSessionId,
            'eventId' => $session->eventId,
            'eventTypeSlug' => $eventTypeSlug,
            'eventTypeId' => $eventTypeId,
            'title' => $session->eventTitle,
            'priceAmount' => $priceData['amount'],
            'isPayWhatYouLike' => $priceData['isPayWhatYouLike'],
            'isHistory' => ($eventTypeId === EventTypeId::History->value),
            'payWhatYouLikeText' => $payWhatYouLikeText,
            'currencySymbol' => $currencySymbol,
            'ctaLabel' => $cta['label'],
            'ctaUrl' => $cta['url'],
            'locationName' => $session->venueName ?? $startPoint,
            'hallName' => $session->hallName ?? '',
            'startDateTime' => $startDateTime,
            'endDateTime' => $endDateTime,
            'isoDate' => $startDateTime->format('Y-m-d'),
            'startTimeIso' => $startDateTime->format('H:i'),
            'endTimeIso' => $endDateTime ? $endDateTime->format('H:i') : '',
            'labels' => $labels,
            'venueName' => $session->venueName ?? '',
            'capacityTotal' => $session->capacityTotal,
            'seatsAvailable' => $session->seatsAvailable,
            'minAge' => $minAge,
            'maxAge' => $maxAge,
            'ageLabel' => AgeLabelFormatter::format($minAge, $maxAge),
            'artistName' => $session->artistName,
            'artistImageUrl' => $session->artistImageUrl,
            'historyTicketLabel' => $session->historyTicketLabel ?? $groupTicketFallback ?: null,
            'timeRange' => $this->computeTimeRange($startDateTime),
            'priceType' => $priceData['isPayWhatYouLike'] ? 'pay-what-you-like' : ($priceData['amount'] === null || $priceData['amount'] == 0 ? 'free' : 'fixed'),
        ];
    }

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
        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::PayWhatYouLike->value) {
                return ['amount' => null, 'isPayWhatYouLike' => true];
            }
        }

        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::Adult->value) {
                return ['amount' => (float)$price->price, 'isPayWhatYouLike' => false];
            }
        }

        if (!empty($prices)) {
            return ['amount' => (float)$prices[0]->price, 'isPayWhatYouLike' => false];
        }

        return ['amount' => null, 'isPayWhatYouLike' => false];
    }

    /** @return string[] */
    private function resolveFilterGroupTypes(string $eventTypeSlug): array
    {
        return match ($eventTypeSlug) {
            'storytelling' => ['day', 'timeRange', 'priceType', 'language', 'ageGroup'],
            'jazz'         => ['day', 'venue', 'priceType'],
            default        => ['day'],
        };
    }

    /** @return string[] */
    private function resolvePriceTypeOptions(string $eventTypeSlug): array
    {
        return $eventTypeSlug === 'jazz'
            ? ['free', 'fixed']
            : ['pay-what-you-like', 'fixed'];
    }

    /**
     * Returns the day numbers (0–6) that are visible for the given event type.
     * Merges global settings with type-specific overrides.
     */
    private function getVisibleDays(int $eventTypeId): array
    {
        $globalSettings = $this->loadGlobalDaySettings();
        $typeSettings = $this->loadTypeDaySettings($eventTypeId);

        return $this->mergeVisibilitySettings($globalSettings, $typeSettings);
    }

    private function loadGlobalDaySettings(): array
    {
        $settings = [];
        foreach ($this->scheduleDayConfigRepository->findConfigs(new ScheduleDayConfigFilter(eventTypeId: 0, orderBy: 'day')) as $row) {
            $settings[$row->dayOfWeek] = $row->isVisible;
        }
        return $settings;
    }

    private function loadTypeDaySettings(int $eventTypeId): array
    {
        $settings = [];
        foreach ($this->scheduleDayConfigRepository->findConfigs(new ScheduleDayConfigFilter(eventTypeId: $eventTypeId, orderBy: 'day')) as $row) {
            $settings[$row->dayOfWeek] = $row->isVisible;
        }
        return $settings;
    }

    private function mergeVisibilitySettings(array $globalSettings, array $typeSettings): array
    {
        $visibleDays = [];
        foreach (DayOfWeek::cases() as $day) {
            $dayValue = $day->value;
            $isVisible = $typeSettings[$dayValue] ?? $globalSettings[$dayValue] ?? true;
            if ($isVisible) {
                $visibleDays[] = $dayValue;
            }
        }
        return $visibleDays;
    }
}
