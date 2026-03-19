<?php

declare(strict_types=1);

namespace App\Mappers;

use App\ViewModels\Schedule\ScheduleDayViewModel;
use App\ViewModels\Schedule\ScheduleEventCardViewModel;
use App\ViewModels\Schedule\ScheduleFilterGroupData;
use App\ViewModels\Schedule\ScheduleFilterOptionData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

final class ScheduleMapper
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
        $eventTypeSlug  = $scheduleData['eventTypeSlug'];
        $eventTypeId    = $scheduleData['eventTypeId'];
        $eventCount     = array_sum(array_map(fn ($day) => count($day->events), $days));
        $headerTexts    = self::resolveHeaderTexts($cmsContent, $pageSlug);
        $cmsSettings    = self::extractCmsSettings($cmsContent);
        $filterGroups   = self::buildFilterGroups($cmsContent, $eventTypeSlug, $days);
        $resetButtonText = self::str($cmsContent, 'schedule_filter_reset_text', 'Reset all filters');

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
            filterGroups: $filterGroups,
            resetButtonText: $resetButtonText,
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
     * @param ScheduleDayViewModel[] $days
     * @return ScheduleFilterGroupData[]
     */
    private static function buildFilterGroups(
        array $cmsContent,
        string $eventTypeSlug,
        array $days,
    ): array {
        $allLabel = self::str($cmsContent, 'schedule_filter_all_label', 'All');
        $groups   = [];

        $groups[] = self::buildDayFilterGroup($cmsContent, $days, $allLabel);

        if ($eventTypeSlug === 'storytelling') {
            $groups[] = self::buildTimeRangeFilterGroup($cmsContent, $allLabel);
            $groups[] = self::buildPriceTypeFilterGroup($cmsContent, $allLabel, $eventTypeSlug);
            $groups[] = self::buildLanguageFilterGroup($cmsContent, $allLabel);
            $groups[] = self::buildAgeGroupFilterGroup($cmsContent);
        } elseif ($eventTypeSlug === 'jazz') {
            $groups[] = self::buildVenueFilterGroup($cmsContent, $allLabel, $days);
            $groups[] = self::buildPriceTypeFilterGroup($cmsContent, $allLabel, $eventTypeSlug);
        }

        return $groups;
    }

    /**
     * @param ScheduleDayViewModel[] $days
     */
    private static function buildDayFilterGroup(array $cmsContent, array $days, string $allLabel): ScheduleFilterGroupData
    {
        $options = [new ScheduleFilterOptionData(label: $allLabel, value: 'all', isDefault: true)];
        foreach ($days as $day) {
            $options[] = new ScheduleFilterOptionData(
                label: $day->dayName,
                value: strtolower($day->dayName),
            );
        }

        return new ScheduleFilterGroupData(
            label: self::str($cmsContent, 'schedule_filter_day_label', 'Day'),
            key: 'day',
            options: $options,
        );
    }

    private static function buildTimeRangeFilterGroup(array $cmsContent, string $allLabel): ScheduleFilterGroupData
    {
        return new ScheduleFilterGroupData(
            label: self::str($cmsContent, 'schedule_filter_time_range_label', 'Time Range'),
            key: 'timeRange',
            options: [
                new ScheduleFilterOptionData(label: $allLabel, value: 'all', isDefault: true),
                new ScheduleFilterOptionData(
                    label: self::str($cmsContent, 'schedule_filter_morning_label', 'Morning (before 12:00)'),
                    value: 'morning',
                ),
                new ScheduleFilterOptionData(
                    label: self::str($cmsContent, 'schedule_filter_afternoon_label', 'Afternoon (12:00 to 17:00)'),
                    value: 'afternoon',
                ),
                new ScheduleFilterOptionData(
                    label: self::str($cmsContent, 'schedule_filter_evening_label', 'Evening (after 17:00)'),
                    value: 'evening',
                ),
            ],
        );
    }

    private static function buildPriceTypeFilterGroup(array $cmsContent, string $allLabel, string $eventTypeSlug): ScheduleFilterGroupData
    {
        $options = [new ScheduleFilterOptionData(label: $allLabel, value: 'all', isDefault: true)];

        if ($eventTypeSlug === 'jazz') {
            $options[] = new ScheduleFilterOptionData(
                label: self::str($cmsContent, 'schedule_filter_free_label', 'Free'),
                value: 'free',
            );
            $options[] = new ScheduleFilterOptionData(
                label: self::str($cmsContent, 'schedule_filter_paid_label', 'Paid'),
                value: 'fixed',
            );
        } else {
            $options[] = new ScheduleFilterOptionData(
                label: self::str($cmsContent, 'schedule_filter_pay_as_you_like_label', 'Pay as you like'),
                value: 'pay-what-you-like',
            );
            $options[] = new ScheduleFilterOptionData(
                label: self::str($cmsContent, 'schedule_filter_fixed_price_label', 'Fixed Price'),
                value: 'fixed',
            );
        }

        return new ScheduleFilterGroupData(
            label: self::str($cmsContent, 'schedule_filter_price_type_label', 'Price Type'),
            key: 'priceType',
            options: $options,
        );
    }

    private static function buildLanguageFilterGroup(array $cmsContent, string $allLabel): ScheduleFilterGroupData
    {
        return new ScheduleFilterGroupData(
            label: self::str($cmsContent, 'schedule_filter_language_label', 'Language'),
            key: 'language',
            options: [
                new ScheduleFilterOptionData(label: $allLabel, value: 'all', isDefault: true),
                new ScheduleFilterOptionData(
                    label: self::str($cmsContent, 'schedule_filter_english_label', 'English'),
                    value: 'english',
                ),
                new ScheduleFilterOptionData(
                    label: self::str($cmsContent, 'schedule_filter_dutch_label', 'Dutch'),
                    value: 'dutch',
                ),
            ],
        );
    }

    private static function buildAgeGroupFilterGroup(array $cmsContent): ScheduleFilterGroupData
    {
        return new ScheduleFilterGroupData(
            label: self::str($cmsContent, 'schedule_filter_age_group_label', 'Age Group'),
            key: 'age',
            options: [
                new ScheduleFilterOptionData(
                    label: self::str($cmsContent, 'schedule_filter_all_ages_label', 'All ages'),
                    value: 'all',
                    isDefault: true,
                ),
                new ScheduleFilterOptionData(label: self::str($cmsContent, 'schedule_filter_age_4_label', '4+'), value: '4'),
                new ScheduleFilterOptionData(label: self::str($cmsContent, 'schedule_filter_age_10_label', '10+'), value: '10'),
                new ScheduleFilterOptionData(label: self::str($cmsContent, 'schedule_filter_age_12_label', '12+'), value: '12'),
                new ScheduleFilterOptionData(label: self::str($cmsContent, 'schedule_filter_age_16_label', '16+'), value: '16'),
            ],
        );
    }

    /**
     * Extracts unique venue names from schedule days for the venue filter.
     *
     * @param ScheduleDayViewModel[] $days
     */
    private static function buildVenueFilterGroup(array $cmsContent, string $allLabel, array $days): ScheduleFilterGroupData
    {
        $venues = [];
        foreach ($days as $day) {
            foreach ($day->events as $event) {
                $name = $event->locationName;
                if ($name !== '' && !isset($venues[$name])) {
                    $venues[$name] = true;
                }
            }
        }

        $options = [new ScheduleFilterOptionData(label: $allLabel, value: 'all', isDefault: true)];
        foreach (array_keys($venues) as $venue) {
            $options[] = new ScheduleFilterOptionData(
                label: $venue,
                value: strtolower($venue),
            );
        }

        return new ScheduleFilterGroupData(
            label: self::str($cmsContent, 'schedule_filter_venue_label', 'Venue'),
            key: 'venue',
            options: $options,
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
