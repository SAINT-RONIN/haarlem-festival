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

        $days = $this->buildDays(
            $scheduleData,
            $eventTypeSlug,
            $eventTypeId,
            $ctaButtonText,
            $payWhatYouLikeText,
            $currencySymbol,
        );

        return [
            'cmsContent' => $cmsContent,
            'pageSlug' => $pageSlug,
            'eventTypeSlug' => $eventTypeSlug,
            'eventTypeId' => $eventTypeId,
            'days' => $days,
        ];
    }

    private function buildDays(
        array  $scheduleData,
        string $eventTypeSlug,
        int    $eventTypeId,
        string $defaultCtaText,
        string $payWhatYouLikeText,
        string $currencySymbol,
    ): array {
        $days = $scheduleData['days'] ?? [];
        $sessions = $scheduleData['sessions'] ?? [];

        if (empty($days)) {
            return [];
        }

        $sessionIds = array_column($sessions, 'EventSessionId');
        $labelsMap = !empty($sessionIds)
            ? $this->labelRepository->findLabels(['sessionIds' => $sessionIds, 'groupBySession' => true])
            : [];
        $pricesMap = !empty($sessionIds)
            ? $this->priceRepository->findPrices(['sessionIds' => $sessionIds, 'groupBySession' => true])
            : [];

        $historyStartPoint = $this->fetchHistoryStartPoint($eventTypeSlug);

        $sessionsByDate = [];
        foreach ($sessions as $session) {
            $sessionsByDate[$session['SessionDate']][] = $session;
        }

        $dayArrays = [];
        foreach ($days as $day) {
            $date = $day['Date'];
            $dateObj = new \DateTimeImmutable($date);
            $daySessions = $sessionsByDate[$date] ?? [];

            $events = [];
            foreach ($daySessions as $session) {
                $events[] = $this->buildEventCard(
                    $session, $eventTypeSlug, $eventTypeId,
                    $labelsMap, $pricesMap,
                    $defaultCtaText, $payWhatYouLikeText, $currencySymbol,
                    $historyStartPoint,
                );
            }

            $events = $this->mergeHistoryEvents($events, $eventTypeSlug);

            $dayArrays[] = [
                'dayName' => $dateObj->format('l'),
                'dateFormatted' => $dateObj->format('l, F j'),
                'isoDate' => $date,
                'events' => $events,
                'isEmpty' => empty($events),
            ];
        }

        return $dayArrays;
    }

    private function fetchHistoryStartPoint(string $eventTypeSlug): ?string
    {
        if ($eventTypeSlug !== 'history') {
            return null;
        }

        $cmsContent = $this->cmsService->getSectionContent('history', 'schedule_section');
        return $this->getStringValue($cmsContent, 'schedule_start_point', 'A giant flag near Church of St. Bavo at Grote Markt');
    }

    private function buildEventCard(
        array   $session,
        string  $eventTypeSlug,
        int     $eventTypeId,
        array   $labelsMap,
        array   $pricesMap,
        string  $defaultCtaText,
        string  $payWhatYouLikeText,
        string  $currencySymbol,
        ?string $historyStartPoint,
    ): array {
        $sessionId = (int)$session['EventSessionId'];
        $startDateTime = new \DateTimeImmutable($session['StartDateTime']);
        $endDateTime = $session['EndDateTime'] ? new \DateTimeImmutable($session['EndDateTime']) : null;

        [$labels, $minAge, $maxAge, $ageLabel] = $this->extractLabels(
            $session, $labelsMap[$sessionId] ?? [], $eventTypeSlug
        );

        $priceResult = $this->extractPriceDisplay($pricesMap[$sessionId] ?? [], $payWhatYouLikeText, $currencySymbol);
        $ctaInfo = $this->buildCtaInfo($session, $eventTypeSlug, $defaultCtaText);
        $locationName = $this->buildLocationName($session, $eventTypeSlug, $historyStartPoint);
        $timeDisplay = $this->buildTimeDisplay($startDateTime, $endDateTime);
        $eventTitle = $eventTypeSlug === 'history' ? $startDateTime->format('H:i') : ($session['EventTitle'] ?? '');

        return [
            'eventSessionId' => $sessionId,
            'eventId' => (int)$session['EventId'],
            'eventTypeSlug' => $eventTypeSlug,
            'eventTypeId' => $eventTypeId,
            'title' => $eventTitle,
            'priceDisplay' => $priceResult['display'],
            'isPayWhatYouLike' => $priceResult['isPayWhatYouLike'],
            'ctaLabel' => $ctaInfo['label'],
            'ctaUrl' => $ctaInfo['url'],
            'locationName' => $locationName,
            'hallName' => $session['HallName'] ?? '',
            'dateDisplay' => $startDateTime->format('l, F j'),
            'isoDate' => $startDateTime->format('Y-m-d'),
            'timeDisplay' => $timeDisplay,
            'startTimeIso' => $startDateTime->format('H:i'),
            'endTimeIso' => $endDateTime ? $endDateTime->format('H:i') : '',
            'labels' => $labels,
            'capacityTotal' => isset($session['CapacityTotal']) ? (int)$session['CapacityTotal'] : null,
            'seatsAvailable' => isset($session['SeatsAvailable']) ? (int)$session['SeatsAvailable'] : null,
            'minAge' => $minAge,
            'maxAge' => $maxAge,
            'ageLabel' => $ageLabel,
            'historyTicketLabel' => $session['HistoryTicketLabel'] ?? null,
            'artistName' => $session['ArtistName'] ?? null,
            'artistImageUrl' => $session['ArtistImageUrl'] ?? null,
            'historyVenue' => $session['HistoryVenue'] ?? null,
            'groupTicketInfo' => $session['GroupTicketInfo'] ?? null,
        ];
    }

    private function extractLabels(array $session, array $sessionLabels, string $eventTypeSlug): array
    {
        $labels = array_map(fn(EventSessionLabel $l) => $l->labelText, $sessionLabels);
        $minAge = isset($session['MinAge']) && (int)$session['MinAge'] > 0 ? (int)$session['MinAge'] : null;
        $maxAge = isset($session['MaxAge']) && (int)$session['MaxAge'] > 0 ? (int)$session['MaxAge'] : null;

        if ($minAge !== null && $maxAge !== null && $minAge > $maxAge) {
            [$minAge, $maxAge] = [$maxAge, $minAge];
        }

        if ($eventTypeSlug !== 'history') {
            $labels = AgeLabelFormatter::appendToLabels($labels, $minAge, $maxAge);
        }

        return [$labels, $minAge, $maxAge, AgeLabelFormatter::format($minAge, $maxAge)];
    }

    private function extractPriceDisplay(array $sessionPrices, string $payWhatYouLikeText, string $currencySymbol): array
    {
        return $this->getPriceDisplay($sessionPrices, $payWhatYouLikeText, $currencySymbol);
    }

    private function buildCtaInfo(array $session, string $eventTypeSlug, string $defaultCtaText): array
    {
        $eventId = (int)$session['EventId'];
        return [
            'label' => !empty($session['CtaLabel']) ? $session['CtaLabel'] : $defaultCtaText,
            'url' => !empty($session['CtaUrl']) ? $session['CtaUrl'] : '/' . $eventTypeSlug . '/' . $eventId,
        ];
    }

    private function buildLocationName(array $session, string $eventTypeSlug, ?string $historyStartPoint): string
    {
        if ($eventTypeSlug === 'history' && $historyStartPoint !== null && $historyStartPoint !== '') {
            return $historyStartPoint;
        }

        return $session['VenueName'] ?? '';
    }

    private function buildTimeDisplay(\DateTimeImmutable $startDateTime, ?\DateTimeImmutable $endDateTime): string
    {
        if ($endDateTime === null) {
            return $startDateTime->format('H:i');
        }

        return $startDateTime->format('H:i') . ' - ' . $endDateTime->format('H:i');
    }

    /**
     * For history schedules, merge events with same start time and title.
     */
    private function mergeHistoryEvents(array $events, string $eventTypeSlug): array
    {
        if ($eventTypeSlug !== 'history' || empty($events)) {
            return $events;
        }

        $grouped = [];

        foreach ($events as $event) {
            $key = $event['startTimeIso'] . '|' . $event['title'];

            if (!isset($grouped[$key])) {
                $grouped[$key] = $event;
                continue;
            }

            $grouped[$key]['labels'] = array_values(
                array_unique(array_merge($grouped[$key]['labels'], $event['labels']))
            );
        }

        return array_values($grouped);
    }

    /**
     * @param EventSessionPrice[] $prices
     */
    private function getPriceDisplay(array $prices, string $payWhatYouLikeText, string $currencySymbol): array
    {
        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::PayWhatYouLike->value) {
                return ['display' => $payWhatYouLikeText, 'isPayWhatYouLike' => true];
            }
        }

        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::Adult->value) {
                return [
                    'display' => $currencySymbol . ' ' . number_format((float)$price->price, 2),
                    'isPayWhatYouLike' => false,
                ];
            }
        }

        if (!empty($prices)) {
            $price = $prices[0];
            return [
                'display' => $currencySymbol . ' ' . number_format((float)$price->price, 2),
                'isPayWhatYouLike' => false,
            ];
        }

        return ['display' => '', 'isPayWhatYouLike' => false];
    }

    private function getStringValue(array $content, string $key, string $default): string
    {
        $value = $content[$key] ?? null;
        return is_string($value) && $value !== '' ? $value : $default;
    }
}
