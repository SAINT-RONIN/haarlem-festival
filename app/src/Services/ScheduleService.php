<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PriceTierId;
use App\Models\EventSessionLabel;
use App\Models\EventSessionPrice;
use App\Repositories\EventSessionLabelRepository;
use App\Repositories\EventSessionPriceRepository;
use App\Repositories\EventSessionRepository;
use App\Repositories\EventTypeRepository;
use App\Services\Interfaces\IScheduleService;
use App\ViewModels\Age\AgeLabelFormatter;
use App\ViewModels\Schedule\ScheduleDayViewModel;
use App\ViewModels\Schedule\ScheduleEventCardViewModel;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * Service for building schedule sections for any event type.
 *
 * This is a global service - not tied to any specific event type.
 */
class ScheduleService implements IScheduleService
{
    private CmsService $cmsService;
    private CmsEventsService $cmsEventsService;
    private EventSessionRepository $sessionRepository;
    private EventSessionLabelRepository $labelRepository;
    private EventSessionPriceRepository $priceRepository;
    private EventTypeRepository $eventTypeRepository;


    public function __construct()
    {
        $this->cmsService = new CmsService();
        $this->cmsEventsService = new CmsEventsService();
        $this->sessionRepository = new EventSessionRepository();
        $this->labelRepository = new EventSessionLabelRepository();
        $this->priceRepository = new EventSessionPriceRepository();
        $this->eventTypeRepository = new EventTypeRepository();
    }

    public function getScheduleData(string $pageSlug, int $eventTypeId, int $maxDays = 4, ?int $eventId = null): array
    {
        $eventType = $this->eventTypeRepository->findEventTypes(['eventTypeId' => $eventTypeId])[0] ?? null;
        $eventTypeSlug = $eventType?->slug ?? $pageSlug;

        $cmsContent = $this->cmsService->getSectionContent($pageSlug, 'schedule_section');
        $visibleDays = $this->cmsEventsService->getVisibleDays($eventTypeId);

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

        $dayViewModels = $this->buildScheduleDays(
            $scheduleData,
            $eventTypeSlug,
            $eventTypeId,
            $ctaButtonText,
            $payWhatYouLikeText,
            $currencySymbol
        );

        $days = [];
        foreach ($dayViewModels as $day) {
            $events = [];
            foreach ($day->events as $event) {
                $events[] = get_object_vars($event);
            }

            $days[] = [
                'dayName' => $day->dayName,
                'dateFormatted' => $day->dateFormatted,
                'isoDate' => $day->isoDate,
                'events' => $events,
                'isEmpty' => $day->isEmpty,
            ];
        }

        return [
            'cmsContent' => $cmsContent,
            'pageSlug' => $pageSlug,
            'eventTypeSlug' => $eventTypeSlug,
            'eventTypeId' => $eventTypeId,
            'days' => $days,
        ];
    }

    /**
     * Builds a schedule section ViewModel for any event type.
     *
     * @param string $pageSlug Page slug for CMS content (e.g., 'storytelling', 'jazz')
     * @param int $eventTypeId Event type ID to filter sessions
     * @param int $maxDays Maximum number of days to show (default 4)
     * @return ScheduleSectionViewModel
     */
    public function buildScheduleSection(string $pageSlug, int $eventTypeId, int $maxDays = 4): ScheduleSectionViewModel
    {
        return ScheduleSectionViewModel::fromData(
            $this->getScheduleData($pageSlug, $eventTypeId, $maxDays)
        );
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
        $sessionIds = array_column($sessions, 'EventSessionId');
        $labelsMap = !empty($sessionIds)
            ? $this->labelRepository->findLabels(['sessionIds' => $sessionIds, 'groupBySession' => true])
            : [];
        $pricesMap = !empty($sessionIds)
            ? $this->priceRepository->findPrices(['sessionIds' => $sessionIds, 'groupBySession' => true])
            : [];

        // Group sessions by date
        $sessionsByDate = [];
        foreach ($sessions as $session) {
            $date = $session['SessionDate'];
            if (!isset($sessionsByDate[$date])) {
                $sessionsByDate[$date] = [];
            }
            $sessionsByDate[$date][] = $session;
        }

        // Build day ViewModels
        $dayViewModels = [];
        foreach ($days as $day) {
            $date = $day['Date'];
            $dateObj = new \DateTimeImmutable($date);
            $daySessions = $sessionsByDate[$date] ?? [];

            $events = [];
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

            $dayViewModels[] = new ScheduleDayViewModel(
                dayName: $dateObj->format('l'),
                dateFormatted: $dateObj->format('l, F j'),
                isoDate: $date,
                events: $events,
                isEmpty: empty($events),
            );
        }

        return $dayViewModels;
    }

    /**
     * Builds an event card ViewModel from session data.
     */
    private function buildEventCard(
        array  $session,
        string $eventTypeSlug,
        int    $eventTypeId,
        array  $labelsMap,
        array  $pricesMap,
        string $defaultCtaText,
        string $payWhatYouLikeText,
        string $currencySymbol
    ): ScheduleEventCardViewModel {
        $sessionId = (int)$session['EventSessionId'];
        $startDateTime = new \DateTimeImmutable($session['StartDateTime']);
        $endDateTime = $session['EndDateTime'] ? new \DateTimeImmutable($session['EndDateTime']) : null;

        // Get labels for this session
        $sessionLabels = $labelsMap[$sessionId] ?? [];
        $labels = array_map(fn (EventSessionLabel $l) => $l->labelText, $sessionLabels);
        $minAge = isset($session['MinAge']) && (int)$session['MinAge'] > 0 ? (int)$session['MinAge'] : null;
        $maxAge = isset($session['MaxAge']) && (int)$session['MaxAge'] > 0 ? (int)$session['MaxAge'] : null;

        if ($minAge !== null && $maxAge !== null && $minAge > $maxAge) {
            [$minAge, $maxAge] = [$maxAge, $minAge];
        }

        $ageLabel = AgeLabelFormatter::format($minAge, $maxAge);
        $labels = AgeLabelFormatter::appendToLabels($labels, $minAge, $maxAge);

        // Get price display
        $sessionPrices = $pricesMap[$sessionId] ?? [];
        $priceResult = $this->getPriceDisplay($sessionPrices, $payWhatYouLikeText, $currencySymbol);

        // CTA label: use session-specific if set, otherwise default
        $ctaLabel = !empty($session['CtaLabel']) ? $session['CtaLabel'] : $defaultCtaText;
        $eventId = (int)$session['EventId'];
        $ctaUrl = !empty($session['CtaUrl']) ? $session['CtaUrl'] : '/' . $eventTypeSlug . '/' . $eventId;

        return new ScheduleEventCardViewModel(
            eventSessionId: $sessionId,
            eventId: $eventId,
            eventTypeSlug: $eventTypeSlug,
            eventTypeId: $eventTypeId,
            title: $session['EventTitle'] ?? '',
            priceDisplay: $priceResult['display'],
            isPayWhatYouLike: $priceResult['isPayWhatYouLike'],
            ctaLabel: $ctaLabel,
            ctaUrl: $ctaUrl,
            locationName: $session['VenueName'] ?? '',
            hallName: $session['HallName'] ?? '',
            dateDisplay: $startDateTime->format('l, F j'),
            isoDate: $startDateTime->format('Y-m-d'),
            timeDisplay: $endDateTime
                ? $startDateTime->format('H:i') . ' - ' . $endDateTime->format('H:i')
                : $startDateTime->format('H:i'),
            startTimeIso: $startDateTime->format('H:i'),
            endTimeIso: $endDateTime ? $endDateTime->format('H:i') : '',
            labels: $labels,
            capacityTotal: isset($session['CapacityTotal']) ? (int)$session['CapacityTotal'] : null,
            seatsAvailable: isset($session['SeatsAvailable']) ? (int)$session['SeatsAvailable'] : null,
            minAge: $minAge,
            maxAge: $maxAge,
            ageLabel: $ageLabel,
            historyTicketLabel: $session['HistoryTicketLabel'] ?? null,
            artistName: $session['ArtistName'] ?? null,
            artistImageUrl: $session['ArtistImageUrl'] ?? null,
        );
    }

    /**
     * Determines price display text.
     *
     * @param EventSessionPrice[] $prices
     */
    private function getPriceDisplay(array $prices, string $payWhatYouLikeText, string $currencySymbol): array
    {
        // Check for PayWhatYouLike tier first
        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::PayWhatYouLike->value) {
                return ['display' => $payWhatYouLikeText, 'isPayWhatYouLike' => true];
            }
        }

        // Check for Adult tier
        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::Adult->value) {
                return [
                    'display' => $currencySymbol . ' ' . number_format((float)$price->price, 2),
                    'isPayWhatYouLike' => false,
                ];
            }
        }

        // Fallback to first available price
        if (!empty($prices)) {
            $price = $prices[0];
            return [
                'display' => $currencySymbol . ' ' . number_format((float)$price->price, 2),
                'isPayWhatYouLike' => false,
            ];
        }

        return ['display' => '', 'isPayWhatYouLike' => false];
    }

    /**
     * Gets a string value from content array with default fallback.
     */
    private function getStringValue(array $content, string $key, string $default): string
    {
        $value = $content[$key] ?? null;
        return is_string($value) && $value !== '' ? $value : $default;
    }
}
