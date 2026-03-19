<?php

declare(strict_types=1);

namespace App\Mappers;

use App\ViewModels\Schedule\ScheduleDayViewModel;
use App\ViewModels\Schedule\ScheduleEventCardViewModel;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

class ScheduleMapper
{
    /**
     * Converts raw schedule data (from ScheduleService::getScheduleData) into a ScheduleSectionViewModel.
     *
     * @param array{cmsContent: array, pageSlug: string, eventTypeSlug: string, eventTypeId: int, days: array} $scheduleData
     */
    public static function toScheduleSection(array $scheduleData): ScheduleSectionViewModel
    {
        $cmsContent = $scheduleData['cmsContent'];
        $pageSlug = $scheduleData['pageSlug'];
        $eventTypeSlug = $scheduleData['eventTypeSlug'];
        $eventTypeId = $scheduleData['eventTypeId'];

        $confirmText = self::str($cmsContent, 'schedule_confirm_text', 'Confirm selection');
        $addingText  = self::str($cmsContent, 'schedule_adding_text', 'Adding...');
        $successText = self::str($cmsContent, 'schedule_success_text', 'Added to program');

        $days = self::mapDays($scheduleData['days'], $confirmText, $addingText, $successText);
        $eventCount = array_sum(array_map(fn ($day) => count($day->events), $days));

        $title = self::str($cmsContent, 'schedule_title', ucfirst($pageSlug) . ' schedule');
        $year = self::str($cmsContent, 'schedule_year', '2026');
        $eventCountLabel = self::str(
            $cmsContent,
            'schedule_event_count_label',
            self::str($cmsContent, 'schedule_story_count_label', 'Events')
        );
        $showEventCount = ($cmsContent['schedule_show_event_count'] ??
            $cmsContent['schedule_show_story_count'] ?? '1') === '1';

        if ($pageSlug === 'history') {
            $year = null;
            $eventCountLabel = null;
            $showEventCount = false;
        }

        return new ScheduleSectionViewModel(
            sectionId: $pageSlug . '-schedule',
            title: $title,
            year: $year,
            eventTypeSlug: $eventTypeSlug,
            eventTypeId: $eventTypeId,
            filtersButtonText: self::str($cmsContent, 'schedule_filters_button_text', 'Filters'),
            showFilters: ($cmsContent['schedule_show_filters'] ?? '1') === '1',
            additionalInfoTitle: self::str($cmsContent, 'schedule_additional_info_title', 'Additional Information:'),
            additionalInfoBody: $cmsContent['schedule_additional_info_body'] ?? '',
            showAdditionalInfo: ($cmsContent['schedule_show_additional_info'] ?? '0') === '1',
            eventCountLabel: $eventCountLabel,
            eventCount: $eventCount,
            showEventCount: $showEventCount,
            ctaButtonText: self::str($cmsContent, 'schedule_cta_button_text', 'Discover'),
            payWhatYouLikeText: self::str($cmsContent, 'schedule_pay_what_you_like_text', 'Pay as you like'),
            currencySymbol: self::str($cmsContent, 'schedule_currency_symbol', '€'),
            noEventsText: self::str($cmsContent, 'schedule_no_events_text', 'No events scheduled'),
            days: $days,
            confirmText: $confirmText,
            addingText: $addingText,
            successText: $successText,
        );
    }

    /**
     * @param array<array<string, mixed>> $rawDays
     * @return ScheduleDayViewModel[]
     */
    private static function mapDays(array $rawDays, string $confirmText, string $addingText, string $successText): array
    {
        $days = [];
        foreach ($rawDays as $day) {
            $isoDate = $day['isoDate'];
            $dateObj = new \DateTimeImmutable($isoDate);
            $events  = [];

            foreach ($day['events'] as $event) {
                $events[] = self::toEventCardViewModel($event, $confirmText, $addingText, $successText);
            }

            $days[] = new ScheduleDayViewModel(
                dayName: $day['dayName'],
                dateFormatted: $dateObj->format('l, F j'),
                isoDate: $isoDate,
                events: $events,
                isEmpty: $day['isEmpty'],
            );
        }
        return $days;
    }

    public static function toEventCardViewModel(
        array $event,
        string $confirmText,
        string $addingText,
        string $successText
    ): ScheduleEventCardViewModel {
        $startDateTime = $event['startDateTime'];
        $endDateTime   = $event['endDateTime'];

        $rawPriceDisplay = self::buildPriceDisplay($event);
        $priceDisplay = ($event['isHistory'] ?? false) && $rawPriceDisplay !== ''
            ? 'from ' . $rawPriceDisplay
            : $rawPriceDisplay;

        $timeDisplay = $endDateTime
            ? $startDateTime->format('H:i') . ' - ' . $endDateTime->format('H:i')
            : $startDateTime->format('H:i');

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
        $cardData['priceDisplay']    = $priceDisplay;
        $cardData['dateDisplay']     = $startDateTime->format('l, F j');
        $cardData['timeDisplay']     = $timeDisplay;
        $cardData['confirmText']     = $confirmText;
        $cardData['addingText']      = $addingText;
        $cardData['successText']     = $successText;

        return new ScheduleEventCardViewModel(...$cardData);
    }

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
        return $symbol . ' ' . number_format((float)$amount, 2);
    }

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

    /**
     * Flattens all events from all schedule days into a single array.
     *
     * @return array<array<string, mixed>>
     */
    public static function flattenEvents(array $scheduleData): array
    {
        $events = [];
        foreach ($scheduleData['days'] ?? [] as $day) {
            foreach ($day['events'] ?? [] as $event) {
                $events[] = $event;
            }
        }
        return $events;
    }

    private static function str(array $content, string $key, string $default): string
    {
        $value = $content[$key] ?? null;
        return is_string($value) && $value !== '' ? $value : $default;
    }
}
