<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Helpers\FormatHelper;
use App\DTOs\Filters\ScheduleFilterParams;
use App\Models\ScheduleSectionContent;
use App\ViewModels\Schedule\ScheduleDayViewModel;
use App\ViewModels\Schedule\ScheduleEventCardViewModel;
use App\ViewModels\Schedule\ScheduleFilterGroupData;
use App\ViewModels\Schedule\ScheduleFilterOptionData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * Transforms raw schedule data arrays (from ScheduleService) into typed
 * ScheduleSectionViewModel trees used by every event-type page's schedule component.
 * Handles day/time/price/language/age/venue filter construction, CMS label resolution,
 * and event-card formatting.
 */
final class ScheduleMapper
{
    private const HISTORY_PAGE_SLUG = 'history';

    /**
     * Converts raw schedule data (from ScheduleService::getScheduleData) into a ScheduleSectionViewModel.
     *
     * @param array{cmsContent: ScheduleSectionContent, pageSlug: string, eventTypeSlug: string, eventTypeId: int, days: array} $scheduleData
     */
    public static function toScheduleSection(array $scheduleData): ScheduleSectionViewModel
    {
        $cmsContent    = $scheduleData['cmsContent'];
        $pageSlug      = $scheduleData['pageSlug'];
        $activeFilters = $scheduleData['activeFilters'] ?? null;
        $availableDays = $scheduleData['availableDays'] ?? [];
        $buttonTexts   = self::extractButtonTexts($cmsContent);
        $days          = self::mapDays($scheduleData['days'], $buttonTexts['confirm'], $buttonTexts['adding'], $buttonTexts['success']);

        return self::buildSectionViewModel($scheduleData, $cmsContent, $pageSlug, $days, $buttonTexts, $activeFilters, $availableDays);
    }

    /**
     * Extracts CMS button labels for the add-to-program interaction.
     *
     * @return array{confirm: string, adding: string, success: string}
     */
    private static function extractButtonTexts(ScheduleSectionContent $cmsContent): array
    {
        return [
            'confirm' => self::str($cmsContent->scheduleConfirmText, 'Confirm selection'),
            'adding'  => self::str($cmsContent->scheduleAddingText, 'Adding...'),
            'success' => self::str($cmsContent->scheduleSuccessText, 'Added to program'),
        ];
    }

    /**
     * Resolves the schedule section header (title, year, event count label).
     * The history page suppresses the year and event count by design.
     *
     * @return array{title: string, year: ?string, eventCountLabel: ?string, showEventCount: bool}
     */
    private static function resolveHeaderTexts(ScheduleSectionContent $cmsContent, string $pageSlug): array
    {
        $title = self::str($cmsContent->scheduleTitle, ucfirst($pageSlug) . ' schedule');
        $year  = self::str($cmsContent->scheduleYear, '2026');
        $eventCountLabel = self::str(
            $cmsContent->scheduleEventCountLabel,
            self::str($cmsContent->scheduleStoryCountLabel, 'Events')
        );
        $showEventCount = ($cmsContent->scheduleShowEventCount ??
            $cmsContent->scheduleShowStoryCount ?? '1') === '1';

        if ($pageSlug === self::HISTORY_PAGE_SLUG) {
            $year            = null;
            $eventCountLabel = null;
            $showEventCount  = false;
        }

        return compact('title', 'year', 'eventCountLabel', 'showEventCount');
    }

    /**
     * Assembles the full ScheduleSectionViewModel from resolved sub-components.
     *
     * @param array{cmsContent: ScheduleSectionContent, pageSlug: string, eventTypeSlug: string, eventTypeId: int, days: array, activeFilters: ?ScheduleFilterParams, availableDays: \App\DTOs\Schedule\ScheduleDayData[]} $scheduleData
     * @param ScheduleDayViewModel[] $days
     * @param array{confirm: string, adding: string, success: string} $buttonTexts
     * @param \App\DTOs\Schedule\ScheduleDayData[] $availableDays
     */
    private static function buildSectionViewModel(
        array $scheduleData,
        ScheduleSectionContent $cmsContent,
        string $pageSlug,
        array $days,
        array $buttonTexts,
        ?ScheduleFilterParams $activeFilters = null,
        array $availableDays = [],
    ): ScheduleSectionViewModel {
        $eventTypeSlug   = $scheduleData['eventTypeSlug'];
        $eventTypeId     = $scheduleData['eventTypeId'];
        $eventCount      = array_sum(array_map(fn ($day) => count($day->events), $days));
        $headerTexts     = self::resolveHeaderTexts($cmsContent, $pageSlug);
        $cmsSettings     = self::extractCmsSettings($cmsContent);
        $filterGroupTypes = $scheduleData['filterGroupTypes'] ?? ['day'];
        $priceTypeOptions = $scheduleData['priceTypeOptions'] ?? ['pay-what-you-like', 'fixed'];
        $filterGroups    = self::buildFilterGroups($cmsContent, $filterGroupTypes, $priceTypeOptions, $days, $activeFilters, $availableDays);
        $resetButtonText = self::str($cmsContent->scheduleFilterResetText, 'Reset all filters');

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
            hasActiveFilters: $activeFilters !== null && $activeFilters->hasAnyFilter(),
            gridClasses: self::resolveGridClasses(count($days)),
            itemClasses: self::resolveItemClasses(count($days)),
        );
    }

    /**
     * Extracts CMS settings for schedule section display toggles and labels.
     *
     * @return array{filtersButtonText: string, showFilters: bool, additionalInfoTitle: string, additionalInfoBody: string, showAdditionalInfo: bool, ctaButtonText: string, payWhatYouLikeText: string, currencySymbol: string, noEventsText: string}
     */
    private static function extractCmsSettings(ScheduleSectionContent $cmsContent): array
    {
        return [
            'filtersButtonText'  => self::str($cmsContent->scheduleFiltersButtonText, 'Filters'),
            'showFilters'        => ($cmsContent->scheduleShowFilters ?? '1') === '1',
            'additionalInfoTitle' => self::str($cmsContent->scheduleAdditionalInfoTitle, 'Additional Information:'),
            'additionalInfoBody' => $cmsContent->scheduleAdditionalInfoBody ?? '',
            'showAdditionalInfo' => ($cmsContent->scheduleShowAdditionalInfo ?? '0') === '1',
            'ctaButtonText'      => self::str($cmsContent->scheduleCtaButtonText, 'Discover'),
            'payWhatYouLikeText' => self::str($cmsContent->schedulePayWhatYouLikeText, 'Pay as you like'),
            'currencySymbol'     => self::str($cmsContent->scheduleCurrencySymbol, '€'),
            'noEventsText'       => self::str($cmsContent->scheduleNoEventsText, 'No events scheduled'),
        ];
    }

    /**
     * Builds all filter groups requested for this event type's schedule section.
     *
     * @param string[] $filterGroupTypes
     * @param string[] $priceTypeOptions
     * @param ScheduleDayViewModel[] $days
     * @param \App\DTOs\Schedule\ScheduleDayData[] $availableDays
     * @return ScheduleFilterGroupData[]
     */
    private static function buildFilterGroups(
        ScheduleSectionContent $cmsContent,
        array $filterGroupTypes,
        array $priceTypeOptions,
        array $days,
        ?ScheduleFilterParams $activeFilters = null,
        array $availableDays = [],
    ): array {
        $allLabel = self::str($cmsContent->scheduleFilterAllLabel, 'All');
        $groups   = [];

        foreach ($filterGroupTypes as $type) {
            $group = match ($type) {
                'day'       => self::buildDayFilterGroup($cmsContent, $availableDays, $allLabel, $activeFilters),
                'timeRange' => self::buildTimeRangeFilterGroup($cmsContent, $allLabel, $activeFilters),
                'priceType' => self::buildPriceTypeFilterGroup($cmsContent, $allLabel, $priceTypeOptions, $activeFilters),
                'language'  => self::buildLanguageFilterGroup($cmsContent, $allLabel, $activeFilters),
                'ageGroup'  => self::buildAgeGroupFilterGroup($cmsContent, $activeFilters),
                'venue'     => self::buildVenueFilterGroup($cmsContent, $allLabel, $days, $activeFilters),
                default     => null,
            };
            if ($group !== null) {
                $groups[] = $group;
            }
        }

        return $groups;
    }

    /**
     * Builds the day filter group from available calendar days.
     *
     * @param \App\DTOs\Schedule\ScheduleDayData[] $availableDays
     */
    private static function buildDayFilterGroup(
        ScheduleSectionContent $cmsContent,
        array $availableDays,
        string $allLabel,
        ?ScheduleFilterParams $activeFilters,
    ): ScheduleFilterGroupData {
        $activeDay = $activeFilters?->day;
        $options   = [new ScheduleFilterOptionData(label: $allLabel, value: 'all', isActive: $activeDay === null)];
        foreach ($availableDays as $dayData) {
            $dayName   = $dayData->dayOfWeek;
            $dayValue  = strtolower($dayName);
            $options[] = new ScheduleFilterOptionData(
                label: $dayName,
                value: $dayValue,
                isActive: $activeDay === $dayValue,
            );
        }

        return new ScheduleFilterGroupData(
            label: self::str($cmsContent->scheduleFilterDayLabel, 'Day'),
            key: 'day',
            options: $options,
        );
    }

    /** Builds the time range filter group (morning/afternoon/evening). */
    private static function buildTimeRangeFilterGroup(
        ScheduleSectionContent $cmsContent,
        string $allLabel,
        ?ScheduleFilterParams $activeFilters,
    ): ScheduleFilterGroupData {
        $active = $activeFilters?->timeRange;
        return new ScheduleFilterGroupData(
            label: self::str($cmsContent->scheduleFilterTimeRangeLabel, 'Time Range'),
            key: 'timeRange',
            options: [
                new ScheduleFilterOptionData(label: $allLabel, value: 'all', isActive: $active === null),
                new ScheduleFilterOptionData(
                    label: self::str($cmsContent->scheduleFilterMorningLabel, 'Morning (before 12:00)'),
                    value: 'morning',
                    isActive: $active === 'morning',
                ),
                new ScheduleFilterOptionData(
                    label: self::str($cmsContent->scheduleFilterAfternoonLabel, 'Afternoon (12:00 to 17:00)'),
                    value: 'afternoon',
                    isActive: $active === 'afternoon',
                ),
                new ScheduleFilterOptionData(
                    label: self::str($cmsContent->scheduleFilterEveningLabel, 'Evening (after 17:00)'),
                    value: 'evening',
                    isActive: $active === 'evening',
                ),
            ],
        );
    }

    /**
     * Builds the price type filter group (free/paid/pay-what-you-like).
     *
     * @param string[] $priceTypeOptions e.g. ['free', 'fixed'] or ['pay-what-you-like', 'fixed']
     */
    private static function buildPriceTypeFilterGroup(
        ScheduleSectionContent $cmsContent,
        string $allLabel,
        array $priceTypeOptions,
        ?ScheduleFilterParams $activeFilters,
    ): ScheduleFilterGroupData {
        $active  = $activeFilters?->priceType;
        $options = [new ScheduleFilterOptionData(label: $allLabel, value: 'all', isActive: $active === null)];

        foreach ($priceTypeOptions as $value) {
            $label = match ($value) {
                'free'              => self::str($cmsContent->scheduleFilterFreeLabel, 'Free'),
                'fixed'             => self::str($cmsContent->scheduleFilterPaidLabel, 'Paid'),
                'pay-what-you-like' => self::str($cmsContent->scheduleFilterPayAsYouLikeLabel, 'Pay as you like'),
                default             => $value,
            };
            $options[] = new ScheduleFilterOptionData(
                label: $label,
                value: $value,
                isActive: $active === $value,
            );
        }

        return new ScheduleFilterGroupData(
            label: self::str($cmsContent->scheduleFilterPriceTypeLabel, 'Price Type'),
            key: 'priceType',
            options: $options,
        );
    }

    /** Builds the language filter group (English/Dutch). */
    private static function buildLanguageFilterGroup(
        ScheduleSectionContent $cmsContent,
        string $allLabel,
        ?ScheduleFilterParams $activeFilters,
    ): ScheduleFilterGroupData {
        $active = $activeFilters?->language;
        return new ScheduleFilterGroupData(
            label: self::str($cmsContent->scheduleFilterLanguageLabel, 'Language'),
            key: 'language',
            options: [
                new ScheduleFilterOptionData(label: $allLabel, value: 'all', isActive: $active === null),
                new ScheduleFilterOptionData(
                    label: self::str($cmsContent->scheduleFilterEnglishLabel, 'English'),
                    value: 'english',
                    isActive: $active === 'english',
                ),
                new ScheduleFilterOptionData(
                    label: self::str($cmsContent->scheduleFilterDutchLabel, 'Dutch'),
                    value: 'dutch',
                    isActive: $active === 'dutch',
                ),
            ],
        );
    }

    /** Builds the age group filter group (All ages, 4+, 10+, 12+, 16+). */
    private static function buildAgeGroupFilterGroup(
        ScheduleSectionContent $cmsContent,
        ?ScheduleFilterParams $activeFilters,
    ): ScheduleFilterGroupData {
        $active = $activeFilters?->age !== null ? (string) $activeFilters->age : null;
        return new ScheduleFilterGroupData(
            label: self::str($cmsContent->scheduleFilterAgeGroupLabel, 'Age Group'),
            key: 'age',
            options: [
                new ScheduleFilterOptionData(
                    label: self::str($cmsContent->scheduleFilterAllAgesLabel, 'All ages'),
                    value: 'all',
                    isActive: $active === null,
                ),
                new ScheduleFilterOptionData(label: self::str($cmsContent->scheduleFilterAge4Label, '4+'), value: '4', isActive: $active === '4'),
                new ScheduleFilterOptionData(label: self::str($cmsContent->scheduleFilterAge10Label, '10+'), value: '10', isActive: $active === '10'),
                new ScheduleFilterOptionData(label: self::str($cmsContent->scheduleFilterAge12Label, '12+'), value: '12', isActive: $active === '12'),
                new ScheduleFilterOptionData(label: self::str($cmsContent->scheduleFilterAge16Label, '16+'), value: '16', isActive: $active === '16'),
            ],
        );
    }

    /**
     * Extracts unique venue names from schedule days for the venue filter.
     *
     * @param ScheduleDayViewModel[] $days
     */
    private static function buildVenueFilterGroup(
        ScheduleSectionContent $cmsContent,
        string $allLabel,
        array $days,
        ?ScheduleFilterParams $activeFilters,
    ): ScheduleFilterGroupData {
        $active = $activeFilters?->venue;
        $venues = [];
        foreach ($days as $day) {
            foreach ($day->events as $event) {
                $name = $event->locationName;
                if ($name !== '' && !isset($venues[$name])) {
                    $venues[$name] = true;
                }
            }
        }

        $options = [new ScheduleFilterOptionData(label: $allLabel, value: 'all', isActive: $active === null)];
        foreach (array_keys($venues) as $venue) {
            $options[] = new ScheduleFilterOptionData(
                label: $venue,
                value: strtolower($venue),
                isActive: $active === strtolower($venue),
            );
        }

        return new ScheduleFilterGroupData(
            label: self::str($cmsContent->scheduleFilterVenueLabel, 'Venue'),
            key: 'venue',
            options: $options,
        );
    }

    /**
     * Maps raw day arrays into ScheduleDayViewModel objects.
     *
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

            $dayNumber = $dateObj->format('j');
            $htmlId = 'schedule-day-' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $day['dayName'])) . '-' . $dayNumber;

            $days[] = new ScheduleDayViewModel(
                dayName: $day['dayName'],
                dateFormatted: $dateObj->format('l, F j'),
                isoDate: $isoDate,
                events: $events,
                isEmpty: $day['isEmpty'],
                htmlId: $htmlId,
            );
        }
        return $days;
    }

    /**
     * Converts a raw event array into a ScheduleEventCardViewModel, formatting
     * price display, time range, and location string with hall/capacity details.
     */
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
        $cardData['dateDisplay']       = $startDateTime->format('l, F j');
        $cardData['timeDisplay']       = self::formatTimeDisplay($startDateTime, $endDateTime);
        $cardData['startTimeDisplay']  = $startDateTime->format('H:i');
        $cardData['endTimeDisplay']    = $endDateTime ? $endDateTime->format('H:i') : '';
        $cardData['confirmText']       = $confirmText;
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

        // History tours show "from <price>" because prices vary by group size
        return ($event['isHistory'] ?? false) && $rawPriceDisplay !== ''
            ? 'from ' . $rawPriceDisplay
            : $rawPriceDisplay;
    }

    /** Returns grid wrapper classes based on how many days are shown (1-4: single row, 5+: wrap). */
    private static function resolveGridClasses(int $dayCount): string
    {
        return $dayCount <= 4 ? 'lg:flex-row lg:flex-nowrap' : 'lg:flex-row lg:flex-wrap';
    }

    /** Returns per-item classes based on how many days are shown. */
    private static function resolveItemClasses(int $dayCount): string
    {
        return $dayCount <= 4 ? 'lg:flex-1' : 'lg:w-[calc(25%-1.5rem)] lg:min-w-[280px]';
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
        return FormatHelper::price((float)$amount, $symbol . ' ');
    }

    /**
     * Builds the location string. Jazz events include hall name and seat capacity
     * (e.g. "Patronaat - Main Hall - 300 seats"); other types show only the venue name.
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

    /**
     * Flattens all events from all schedule days into typed ScheduleEventCardViewModels.
     *
     * @return ScheduleEventCardViewModel[]
     */
    public static function flattenEventsAsViewModels(array $scheduleData): array
    {
        $viewModels = [];
        foreach ($scheduleData['days'] ?? [] as $day) {
            foreach ($day['events'] ?? [] as $event) {
                $viewModels[] = self::toEventCardViewModel($event, '', '', '');
            }
        }
        return $viewModels;
    }

    /** Returns a non-empty string value, or the default when null/empty. */
    private static function str(?string $value, string $default): string
    {
        return $value !== null && $value !== '' ? $value : $default;
    }
}
