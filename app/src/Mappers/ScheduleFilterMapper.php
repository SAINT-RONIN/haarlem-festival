<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Filters\ScheduleFilterParams;
use App\Models\ScheduleSectionContent;
use App\ViewModels\Schedule\ScheduleDayViewModel;
use App\ViewModels\Schedule\ScheduleFilterGroupData;
use App\ViewModels\Schedule\ScheduleFilterOptionData;

/**
 * Builds schedule filter groups (day, time, price, language, age, venue)
 * from CMS content and active filter state.
 */
final class ScheduleFilterMapper
{
    /**
     * Builds all filter groups requested for this event type's schedule section.
     *
     * @param string[] $filterGroupTypes
     * @param string[] $priceTypeOptions
     * @param ScheduleDayViewModel[] $days
     * @param \App\DTOs\Schedule\ScheduleDayData[] $availableDays
     * @return ScheduleFilterGroupData[]
     */
    public static function buildFilterGroups(
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

    /** Returns a non-empty string value, or the default when null/empty. */
    private static function str(?string $value, string $default): string
    {
        return $value !== null && $value !== '' ? $value : $default;
    }
}
