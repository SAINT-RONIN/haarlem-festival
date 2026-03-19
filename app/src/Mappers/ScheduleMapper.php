<?php

declare(strict_types=1);

namespace App\Mappers;

use App\ViewModels\Schedule\ScheduleDayViewModel;
use App\ViewModels\Schedule\ScheduleEventCardViewModel;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

class ScheduleMapper
{
    private const HISTORY_PAGE_SLUG = 'history';

    /**
     * Converts raw schedule data (from ScheduleService::getScheduleData) into a ScheduleSectionViewModel.
     *
     * @param array{cmsContent: array, pageSlug: string, eventTypeSlug: string, eventTypeId: int, days: array} $scheduleData
     */
    public static function toScheduleSection(array $scheduleData): ScheduleSectionViewModel
    {
        $cmsContent  = $scheduleData['cmsContent'];
        $pageSlug    = $scheduleData['pageSlug'];
        $buttonTexts = self::extractButtonTexts($cmsContent);
        $days        = self::mapDays($scheduleData['days'], $buttonTexts['confirm'], $buttonTexts['adding'], $buttonTexts['success']);

        return self::buildSectionViewModel($scheduleData, $cmsContent, $pageSlug, $days, $buttonTexts);
    }

    /**
     * @param array<string, mixed> $cmsContent
     * @return array{confirm: string, adding: string, success: string}
     */
    private static function extractButtonTexts(array $cmsContent): array
    {
        return [
            'confirm' => self::str($cmsContent, 'schedule_confirm_text', 'Confirm selection'),
            'adding'  => self::str($cmsContent, 'schedule_adding_text', 'Adding...'),
            'success' => self::str($cmsContent, 'schedule_success_text', 'Added to program'),
        ];
    }

    /**
     * @param array<string, mixed> $cmsContent
     * @return array{title: string, year: ?string, eventCountLabel: ?string, showEventCount: bool}
     */
    private static function resolveHeaderTexts(array $cmsContent, string $pageSlug): array
    {
        $title = self::str($cmsContent, 'schedule_title', ucfirst($pageSlug) . ' schedule');
        $year  = self::str($cmsContent, 'schedule_year', '2026');
        $eventCountLabel = self::str(
            $cmsContent,
            'schedule_event_count_label',
            self::str($cmsContent, 'schedule_story_count_label', 'Events')
        );
        $showEventCount = ($cmsContent['schedule_show_event_count'] ??
            $cmsContent['schedule_show_story_count'] ?? '1') === '1';

        if ($pageSlug === self::HISTORY_PAGE_SLUG) {
            $year            = null;
            $eventCountLabel = null;
            $showEventCount  = false;
        }

        return compact('title', 'year', 'eventCountLabel', 'showEventCount');
    }

    /**
     * @param array{cmsContent: array, pageSlug: string, eventTypeSlug: string, eventTypeId: int, days: array} $scheduleData
     * @param array<string, mixed> $cmsContent
     * @param ScheduleDayViewModel[] $days
     * @param array{confirm: string, adding: string, success: string} $buttonTexts
     */
    private static function buildSectionViewModel(
        array $scheduleData,
        array $cmsContent,
        string $pageSlug,
        array $days,
        array $buttonTexts,
    ): ScheduleSectionViewModel {
        $eventTypeSlug = $scheduleData['eventTypeSlug'];
        $eventTypeId   = $scheduleData['eventTypeId'];
        $eventCount    = array_sum(array_map(fn ($day) => count($day->events), $days));
        $headerTexts   = self::resolveHeaderTexts($cmsContent, $pageSlug);
        $cmsSettings   = self::extractCmsSettings($cmsContent);

        return new ScheduleSectionViewModel(
            sectionId: $pageSlug . '-schedule',
            title: $headerTexts['title'],
            year: $headerTexts['year'],
            eventTypeSlug: $eventTypeSlug,
            eventTypeId: $eventTypeId,
            filtersButtonText: $cmsSettings['filtersButtonText'],
            showFilters: $cmsSettings['showFilters'],
            additionalInfoTitle: $cmsSettings['additionalInfoTitle'],
            additionalInfoBody: $cmsSettings['additionalInfoBody'],
            showAdditionalInfo: $cmsSettings['showAdditionalInfo'],
            eventCountLabel: $headerTexts['eventCountLabel'],
            eventCount: $eventCount,
            showEventCount: $headerTexts['showEventCount'],
            ctaButtonText: $cmsSettings['ctaButtonText'],
            payWhatYouLikeText: $cmsSettings['payWhatYouLikeText'],
            currencySymbol: $cmsSettings['currencySymbol'],
            noEventsText: $cmsSettings['noEventsText'],
            days: $days,
            confirmText: $buttonTexts['confirm'],
            addingText: $buttonTexts['adding'],
            successText: $buttonTexts['success'],
        );
    }

    /**
     * @param array<string, mixed> $cmsContent
     * @return array{filtersButtonText: string, showFilters: bool, additionalInfoTitle: string, additionalInfoBody: string, showAdditionalInfo: bool, ctaButtonText: string, payWhatYouLikeText: string, currencySymbol: string, noEventsText: string}
     */
    private static function extractCmsSettings(array $cmsContent): array
    {
        return [
            'filtersButtonText'  => self::str($cmsContent, 'schedule_filters_button_text', 'Filters'),
            'showFilters'        => ($cmsContent['schedule_show_filters'] ?? '1') === '1',
            'additionalInfoTitle' => self::str($cmsContent, 'schedule_additional_info_title', 'Additional Information:'),
            'additionalInfoBody' => $cmsContent['schedule_additional_info_body'] ?? '',
            'showAdditionalInfo' => ($cmsContent['schedule_show_additional_info'] ?? '0') === '1',
            'ctaButtonText'      => self::str($cmsContent, 'schedule_cta_button_text', 'Discover'),
            'payWhatYouLikeText' => self::str($cmsContent, 'schedule_pay_what_you_like_text', 'Pay as you like'),
            'currencySymbol'     => self::str($cmsContent, 'schedule_currency_symbol', '€'),
            'noEventsText'       => self::str($cmsContent, 'schedule_no_events_text', 'No events scheduled'),
        ];
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
        $cardData['dateDisplay']     = $startDateTime->format('l, F j');
        $cardData['timeDisplay']     = self::formatTimeDisplay($startDateTime, $endDateTime);
        $cardData['confirmText']     = $confirmText;
        $cardData['addingText']      = $addingText;
        $cardData['successText']     = $successText;

        return $cardData;
    }

    /**
     * @param array<string, mixed> $event
     */
    private static function formatPriceDisplay(array $event): string
    {
        $rawPriceDisplay = self::buildPriceDisplay($event);

        return ($event['isHistory'] ?? false) && $rawPriceDisplay !== ''
            ? 'from ' . $rawPriceDisplay
            : $rawPriceDisplay;
    }

    private static function formatTimeDisplay(\DateTimeInterface $start, ?\DateTimeInterface $end): string
    {
        return $end
            ? $start->format('H:i') . ' - ' . $end->format('H:i')
            : $start->format('H:i');
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
