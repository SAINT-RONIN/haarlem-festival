<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Constants\JazzPageConstants;
use App\Constants\ScheduleConstants;
use App\DTOs\Domain\Schedule\ScheduleDisplayStrings;
use App\DTOs\Domain\Schedule\SessionCtaResult;
use App\DTOs\Domain\Schedule\SessionPriceResult;
use App\DTOs\Domain\Schedule\SessionWithEvent;
use App\Enums\EventTypeId;
use App\Enums\PriceTierId;
use App\Helpers\AgeLabelFormatter;
use App\Helpers\FormatHelper;
use App\Models\EventSessionLabel;
use App\Models\EventSessionPrice;
use App\ViewModels\Schedule\ScheduleEventCardViewModel;

/**
 * Transforms session data into event card arrays and ScheduleEventCardViewModels.
 *
 * Two-step pipeline: buildEventCardArray() produces the raw data array stored in
 * ScheduleSectionData; toEventCardViewModel() adds formatted display fields and
 * converts it to a ViewModel.
 */
final class ScheduleCardMapper
{
    /**
     * Builds the raw event card array for one session.
     * Called by ScheduleService when assembling schedule days.
     *
     * @param array<int, EventSessionLabel[]> $labelsMap
     * @param array<int, EventSessionPrice[]> $pricesMap
     * @return array<string, mixed>
     */
    public static function buildEventCardArray(
        SessionWithEvent $session,
        string $eventTypeSlug,
        int $eventTypeId,
        array $labelsMap,
        array $pricesMap,
        ScheduleDisplayStrings $displayStrings,
        array $historyTourOptions = [],
    ): array {
        $sessionId = $session->eventSessionId;

        [$minAge, $maxAge] = self::resolveAgeRange($session);
        $labels    = self::extractLabels($labelsMap[$sessionId] ?? [], $minAge, $maxAge, $eventTypeId);
        $priceData = self::resolvePrice($pricesMap[$sessionId] ?? []);
        $cta       = self::resolveCta($session, $eventTypeSlug, $displayStrings->ctaButtonText);

        return array_merge(
            self::buildCardIdentityFields($session, $eventTypeSlug, $eventTypeId),
            self::buildCardPriceFields($priceData, $cta, $displayStrings),
            self::buildCardLocationFields($session, $displayStrings->startPoint),
            self::buildCardTimeFields($session),
            self::buildCardDetailFields($session, $labels, $minAge, $maxAge, $displayStrings->groupTicketFallback, $historyTourOptions),
        );
    }

    /**
     * Enriches a raw event card array with formatted display fields and wraps it in a ViewModel.
     * Called by ScheduleDayMapper when converting schedule days to ViewModels.
     *
     * @param array<string, mixed> $event
     */
    public static function toEventCardViewModel(
        array $event,
        string $confirmText,
        string $addingText,
        string $successText,
    ): ScheduleEventCardViewModel {
        return new ScheduleEventCardViewModel(...self::buildCardData($event, $confirmText, $addingText, $successText));
    }

    // -------------------------------------------------------------------------
    // Card array builders (used by buildEventCardArray)
    // -------------------------------------------------------------------------

    /** Core identity fields for the event card. */
    private static function buildCardIdentityFields(SessionWithEvent $session, string $eventTypeSlug, int $eventTypeId): array
    {
        return [
            'eventSessionId' => $session->eventSessionId,
            'eventId'        => $session->eventId,
            'eventTypeSlug'  => $eventTypeSlug,
            'eventTypeId'    => $eventTypeId,
            'title'          => $session->eventTitle,
            'isHistory'      => ($eventTypeId === EventTypeId::History->value),
        ];
    }

    /** Pricing and CTA fields for the event card. */
    private static function buildCardPriceFields(SessionPriceResult $priceData, SessionCtaResult $cta, ScheduleDisplayStrings $displayStrings): array
    {
        return [
            'priceAmount'        => $priceData->amount,
            'isPayWhatYouLike'   => $priceData->isPayWhatYouLike,
            'payWhatYouLikeText' => $displayStrings->payWhatYouLikeText,
            'currencySymbol'     => $displayStrings->currencySymbol,
            'ctaLabel'           => $cta->label,
            'ctaUrl'             => $cta->url,
            'priceType'          => self::resolvePriceType($priceData),
        ];
    }

    /** Location fields for the event card. */
    private static function buildCardLocationFields(SessionWithEvent $session, string $startPoint): array
    {
        return [
            'locationName' => $session->venueName ?? $startPoint,
            'hallName'     => $session->hallName ?? '',
            'venueName'    => $session->venueName ?? '',
        ];
    }

    /** DateTime fields for the event card. */
    private static function buildCardTimeFields(SessionWithEvent $session): array
    {
        return [
            'startDateTime' => $session->startDateTime,
            'endDateTime'   => $session->endDateTime,
            'isoDate'       => $session->startDateTime->format('Y-m-d'),
            'startTimeIso'  => $session->startDateTime->format('H:i'),
            'endTimeIso'    => $session->endDateTime ? $session->endDateTime->format('H:i') : '',
            'timeRange'     => self::computeTimeRange($session->startDateTime),
        ];
    }

    /** Remaining detail fields for the event card: labels, capacity, age, artist, history. */
    private static function buildCardDetailFields(
        SessionWithEvent $session,
        array $labels,
        ?int $minAge,
        ?int $maxAge,
        string $groupTicketFallback,
        array $historyTourOptions = [],
    ): array {
        return [
            'labels'             => $labels,
            'capacityTotal'      => $session->capacityTotal,
            'seatsAvailable'     => $session->seatsAvailable,
            'minAge'             => $minAge,
            'maxAge'             => $maxAge,
            'ageLabel'           => AgeLabelFormatter::format($minAge, $maxAge),
            'artistName'         => $session->artistName,
            'artistImageUrl'     => $session->artistImageUrl,
            'historyTicketLabel' => $session->historyTicketLabel ?? $groupTicketFallback ?: null,
            'historyTourOptions' => $historyTourOptions,
        ];
    }

    /**
     * Swaps min/max if they are stored inverted in the database.
     *
     * @return array{0: ?int, 1: ?int}
     */
    private static function resolveAgeRange(SessionWithEvent $session): array
    {
        $minAge = $session->minAge !== null && $session->minAge > 0 ? $session->minAge : null;
        $maxAge = $session->maxAge !== null && $session->maxAge > 0 ? $session->maxAge : null;

        if ($minAge !== null && $maxAge !== null && $minAge > $maxAge) {
            [$minAge, $maxAge] = [$maxAge, $minAge];
        }

        return [$minAge, $maxAge];
    }

    /**
     * Converts label models to plain strings and appends an age-range label,
     * except for History events which handle age labels at the tour-options level.
     *
     * @param EventSessionLabel[] $sessionLabels
     * @return string[]
     */
    private static function extractLabels(array $sessionLabels, ?int $minAge, ?int $maxAge, int $eventTypeId): array
    {
        $labels = array_map(fn(EventSessionLabel $l) => $l->labelText, $sessionLabels);

        if ($eventTypeId === EventTypeId::History->value) {
            return $labels;
        }

        return AgeLabelFormatter::appendToLabels($labels, $minAge, $maxAge);
    }

    /**
     * Falls back to a convention-based URL (/{eventTypeSlug}/{eventSlug}) when no CTA is set on the session.
     */
    private static function resolveCta(SessionWithEvent $session, string $eventTypeSlug, string $defaultCtaText): SessionCtaResult
    {
        return new SessionCtaResult(
            label: !empty($session->ctaLabel) ? $session->ctaLabel : $defaultCtaText,
            url: !empty($session->ctaUrl) ? $session->ctaUrl : '/' . $eventTypeSlug . '/' . $session->eventSlug,
        );
    }

    /**
     * Picks the best display price from the session price list.
     * Priority: pay-what-you-like > Adult tier > Single tier > first available tier.
     * Single is preferred over Group so History "from" price shows the cheaper entry price.
     *
     * @param EventSessionPrice[] $prices
     */
    private static function resolvePrice(array $prices): SessionPriceResult
    {
        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::PayWhatYouLike->value) {
                return new SessionPriceResult(amount: null, isPayWhatYouLike: true);
            }
        }

        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::Adult->value) {
                return new SessionPriceResult(amount: (float) $price->price, isPayWhatYouLike: false);
            }
        }

        foreach ($prices as $price) {
            if ($price->priceTierId === PriceTierId::Single->value) {
                return new SessionPriceResult(amount: (float) $price->price, isPayWhatYouLike: false);
            }
        }

        if (!empty($prices)) {
            return new SessionPriceResult(amount: (float) $prices[0]->price, isPayWhatYouLike: false);
        }

        return new SessionPriceResult(amount: null, isPayWhatYouLike: false);
    }

    private static function resolvePriceType(SessionPriceResult $priceData): string
    {
        if ($priceData->isPayWhatYouLike) {
            return ScheduleConstants::PRICE_TYPE_PAY_WHAT_YOU_LIKE;
        }

        return ($priceData->amount === null || $priceData->amount == 0)
            ? ScheduleConstants::PRICE_TYPE_FREE
            : ScheduleConstants::PRICE_TYPE_FIXED;
    }

    /** Maps a start time to a time-of-day bucket for filtering (morning / afternoon / evening). */
    private static function computeTimeRange(\DateTimeInterface $startDateTime): string
    {
        $hour = (int) $startDateTime->format('G');
        if ($hour < ScheduleConstants::MORNING_HOUR_END) {
            return ScheduleConstants::TIME_RANGE_MORNING;
        }
        return $hour < ScheduleConstants::AFTERNOON_HOUR_END
            ? ScheduleConstants::TIME_RANGE_AFTERNOON
            : ScheduleConstants::TIME_RANGE_EVENING;
    }

    // -------------------------------------------------------------------------
    // ViewModel conversion (used by toEventCardViewModel)
    // -------------------------------------------------------------------------

    /**
     * @param array<string, mixed> $event
     * @return array<string, mixed>
     */
    private static function buildCardData(
        array $event,
        string $confirmText,
        string $addingText,
        string $successText,
    ): array {
        $startDateTime = $event['startDateTime'];
        $endDateTime   = $event['endDateTime'];

        $cardData = $event;
        unset(
            $cardData['priceAmount'],
            $cardData['payWhatYouLikeText'],
            $cardData['currencySymbol'],
            $cardData['isHistory'],
            $cardData['startDateTime'],
            $cardData['endDateTime'],
            $cardData['venueName']
        );

        $cardData['locationDisplay']  = self::buildLocationDisplay($event);
        $cardData['locationName']     = $event['locationName'];
        $cardData['priceDisplay']     = self::formatPriceDisplay($event);
        $cardData['dateDisplay']      = $startDateTime->format('l, F j');
        $cardData['timeDisplay']      = self::formatTimeDisplay($startDateTime, $endDateTime);
        $cardData['startTimeDisplay'] = $startDateTime->format('H:i');
        $cardData['endTimeDisplay']   = $endDateTime ? $endDateTime->format('H:i') : '';
        $cardData['confirmText']      = $confirmText;
        $cardData['addingText']       = $addingText;
        $cardData['successText']      = $successText;
        $cardData['datetime']         = $startDateTime;

        return $cardData;
    }

    /**
     * Formats the price display, prepending "from" for history tours.
     *
     * @param array<string, mixed> $event
     */
    private static function formatPriceDisplay(array $event): string
    {
        $rawPriceDisplay = self::buildPriceDisplay($event);

        // History tours show "from <price>" because prices vary by group size
        return ($event['isHistory'] ?? false) && $rawPriceDisplay !== ''
            ? 'from ' . $rawPriceDisplay
            : $rawPriceDisplay;
    }

    /** Formats a start-end time pair into "HH:MM - HH:MM". */
    private static function formatTimeDisplay(\DateTimeInterface $start, ?\DateTimeInterface $end): string
    {
        return $end
            ? $start->format('H:i') . ' - ' . $end->format('H:i')
            : $start->format('H:i');
    }

    /**
     * Resolves the price display string: pay-what-you-like text, formatted amount, or empty.
     *
     * @param array<string, mixed> $event
     */
    private static function buildPriceDisplay(array $event): string
    {
        if ($event['isPayWhatYouLike']) {
            return (string) ($event['payWhatYouLikeText'] ?? '');
        }

        $amount = $event['priceAmount'] ?? null;
        if ($amount === null) {
            return '';
        }

        $symbol = (string) ($event['currencySymbol'] ?? '€');
        return FormatHelper::price((float) $amount, $symbol . ' ');
    }

    /**
     * Builds the location string. Jazz events include hall name and seat capacity
     * (e.g. "Patronaat • Main Hall • 300 seats"); other types show only the venue name.
     *
     * @param array<string, mixed> $event
     */
    private static function buildLocationDisplay(array $event): string
    {
        $eventTypeSlug = (string) ($event['eventTypeSlug'] ?? '');
        $locationName  = (string) ($event['locationName'] ?? '');
        $hallName      = (string) ($event['hallName'] ?? '');
        $capacityTotal = (int) ($event['capacityTotal'] ?? 0);

        if ($eventTypeSlug === JazzPageConstants::PAGE_SLUG && $hallName !== '') {
            return implode(' • ', array_filter([
                $locationName,
                $hallName,
                $capacityTotal > 0 ? $capacityTotal . ' seats' : null,
            ]));
        }

        return $locationName;
    }
}
