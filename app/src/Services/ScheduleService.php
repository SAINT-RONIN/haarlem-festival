<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\DayOfWeek;
use App\Enums\EventTypeId;
use App\Enums\PriceTierId;
use App\Models\EventSessionLabel;
use App\Models\EventSessionPrice;
use App\Repositories\CmsContentRepository;
use App\Repositories\EventSessionLabelRepository;
use App\Repositories\EventSessionPriceRepository;
use App\Repositories\EventSessionRepository;
use App\Repositories\EventTypeRepository;
use App\Repositories\ScheduleDayConfigRepository;
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
        private CmsContentRepository $cmsService,
        private EventSessionRepository $sessionRepository,
        private EventSessionLabelRepository $labelRepository,
        private EventSessionPriceRepository $priceRepository,
        private EventTypeRepository $eventTypeRepository,
        private ScheduleDayConfigRepository $scheduleDayConfigRepository,
    ) {
    }

    public function getScheduleData(string $pageSlug, int $eventTypeId, int $maxDays = 4, ?int $eventId = null): array
    {
        $eventType = $this->eventTypeRepository->findEventTypes(['eventTypeId' => $eventTypeId])[0] ?? null;
        $eventTypeSlug = $eventType?->slug ?? $pageSlug;

        $cmsContent = $this->cmsService->getSectionContent($pageSlug, 'schedule_section');
        $visibleDays = $this->getVisibleDays($eventTypeId);

        $filters = [
            'eventTypeId' => $eventTypeId,
            'isActive' => true,
            'eventIsActive' => true,
            'includeCancelled' => false,
            'groupByDay' => true,
            'maxDays' => $maxDays,
            'visibleDays' => $visibleDays,
            'orderBy' => 'es.StartDateTime ASC',
        ];

        if ($eventId !== null) {
            $filters['eventId'] = $eventId;
        }

        $scheduleData = $this->sessionRepository->findSessions($filters);

        $ctaButtonText = $this->getStringValue($cmsContent, 'schedule_cta_button_text', 'Discover');
        $payWhatYouLikeText = $this->getStringValue($cmsContent, 'schedule_pay_what_you_like_text', 'Pay as you like');
        $currencySymbol = $this->getStringValue($cmsContent, 'schedule_currency_symbol', '€');

        $days = $this->buildScheduleDays(
            $scheduleData,
            $eventTypeSlug,
            $eventTypeId,
            $ctaButtonText,
            $payWhatYouLikeText,
            $currencySymbol
        );

        return [
            'cmsContent' => $cmsContent,
            'pageSlug' => $pageSlug,
            'eventTypeSlug' => $eventTypeSlug,
            'eventTypeId' => $eventTypeId,
            'days' => $days,
        ];
    }

    /**
     * Builds day ViewModels from schedule data.
     */
    private function buildScheduleDays(
        array  $scheduleData,
        string $eventTypeSlug,
        int    $eventTypeId,
        string $defaultCtaText,
        string $payWhatYouLikeText,
        string $currencySymbol
    ): array {
        $days = $scheduleData['days'] ?? [];
        $sessions = $scheduleData['sessions'] ?? [];

        if (empty($days)) {
            return [];
        }

        // Get session IDs for batch loading labels and prices
        $sessionIds = array_map(static fn ($s) => $s->eventSessionId, $sessions);
        $labelsMap = !empty($sessionIds)
            ? $this->labelRepository->findLabels(['sessionIds' => $sessionIds, 'groupBySession' => true])
            : [];
        $pricesMap = !empty($sessionIds)
            ? $this->priceRepository->findPrices(['sessionIds' => $sessionIds, 'groupBySession' => true])
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
                $daySessions = $this->groupSessionsByTimeSlot($daySessions, $labelsMap);
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
                    $currencySymbol
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
     * @return array Merged sessions (one per time slot)
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
        return array_values($grouped);
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
        string $currencySymbol
    ): array {
        $sessionId = $session->eventSessionId;
        [$minAge, $maxAge] = $this->resolveAgeRange($session);
        $labels = $this->extractLabels($labelsMap[$sessionId] ?? [], $minAge, $maxAge);
        $priceData = $this->resolvePrice($pricesMap[$sessionId] ?? []);
        $cta = $this->resolveCta($session, $eventTypeSlug, $defaultCtaText);

        return $this->buildCardArray($session, $eventTypeSlug, $eventTypeId, $labels, $minAge, $maxAge, $priceData, $cta, $payWhatYouLikeText, $currencySymbol);
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
        $url = !empty($session->ctaUrl) ? $session->ctaUrl : '/' . $eventTypeSlug . '/' . $session->eventId;
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
        string $currencySymbol
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
            'locationName' => $session->venueName ?? '',
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
            'historyTicketLabel' => $session->historyTicketLabel,
        ];
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

    /**
     * Gets a string value from content array with default fallback.
     */
    private function getStringValue(array $content, string $key, string $default): string
    {
        $value = $content[$key] ?? null;
        return is_string($value) && $value !== '' ? $value : $default;
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
        foreach ($this->scheduleDayConfigRepository->findConfigs(['eventTypeId' => null, 'orderBy' => 'day']) as $row) {
            $settings[$row->dayOfWeek] = $row->isVisible;
        }
        return $settings;
    }

    private function loadTypeDaySettings(int $eventTypeId): array
    {
        $settings = [];
        foreach ($this->scheduleDayConfigRepository->findConfigs(['eventTypeId' => $eventTypeId, 'orderBy' => 'day']) as $row) {
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
