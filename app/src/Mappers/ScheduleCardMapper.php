<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Helpers\FormatHelper;
use App\ViewModels\Schedule\ScheduleEventCardViewModel;

/**
 * Transforms raw event arrays into ScheduleEventCardViewModels, formatting
 * price display, time range, and location string with hall/capacity details.
 */
final class ScheduleCardMapper
{
    /**
     * Converts a raw event array into a ScheduleEventCardViewModel.
     *
     * @param array<string, mixed> $event
     */
    public static function toEventCardViewModel(
        array $event,
        string $confirmText,
        string $addingText,
        string $successText,
    ): ScheduleEventCardViewModel {
        $cardData = self::buildCardData($event, $confirmText, $addingText, $successText);

        return new ScheduleEventCardViewModel(...$cardData);
    }

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

        $cardData['locationDisplay'] = self::buildLocationDisplay($event);
        $cardData['locationName']    = $event['locationName'];
        $cardData['priceDisplay']    = self::formatPriceDisplay($event);
        $cardData['dateDisplay']       = $startDateTime->format('l, F j');
        $cardData['timeDisplay']       = self::formatTimeDisplay($startDateTime, $endDateTime);
        $cardData['startTimeDisplay']  = $startDateTime->format('H:i');
        $cardData['endTimeDisplay']    = $endDateTime ? $endDateTime->format('H:i') : '';
        $cardData['confirmText']       = $confirmText;
        $cardData['addingText']      = $addingText;
        $cardData['successText']     = $successText;
        $cardData['datetime'] = $startDateTime;

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
            return (string)($event['payWhatYouLikeText'] ?? '');
        }

        $amount = $event['priceAmount'] ?? null;
        if ($amount === null) {
            return '';
        }

        $symbol = (string)($event['currencySymbol'] ?? '€');
        return FormatHelper::price((float)$amount, $symbol . ' ');
    }

    /**
     * Builds the location string. Jazz events include hall name and seat capacity
     * (e.g. "Patronaat - Main Hall - 300 seats"); other types show only the venue name.
     *
     * @param array<string, mixed> $event
     */
    private static function buildLocationDisplay(array $event): string
    {
        $eventTypeSlug = (string)($event['eventTypeSlug'] ?? '');
        $locationName  = (string)($event['locationName'] ?? '');
        $hallName      = (string)($event['hallName'] ?? '');
        $capacityTotal = (int)($event['capacityTotal'] ?? 0);

        if ($eventTypeSlug === 'jazz' && $hallName !== '') {
            return implode(' • ', array_filter([
                $locationName,
                $hallName,
                $capacityTotal > 0 ? $capacityTotal . ' seats' : null,
            ]));
        }

        return $locationName;
    }
}
