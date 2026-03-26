<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Filters\ScheduleFilterParams;
use App\Models\ScheduleSectionContent;
use App\ViewModels\Schedule\ScheduleDayViewModel;
use App\ViewModels\Schedule\ScheduleEventCardViewModel;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * Assembles schedule section and day ViewModels from CMS content and raw day arrays,
 * delegating filter groups to ScheduleFilterMapper and event cards to ScheduleCardMapper.
 */
final class ScheduleDayMapper
{
    private const HISTORY_PAGE_SLUG = 'history';

    /**
     * Assembles the full ScheduleSectionViewModel from resolved sub-components.
     *
     * @param array{cmsContent: ScheduleSectionContent, pageSlug: string, eventTypeSlug: string, eventTypeId: int, days: array, activeFilters: ?ScheduleFilterParams, availableDays: \App\DTOs\Schedule\ScheduleDayData[]} $scheduleData
     */
    public static function buildSection(array $scheduleData): ScheduleSectionViewModel
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
                $viewModels[] = ScheduleCardMapper::toEventCardViewModel($event, '', '', '');
            }
        }
        return $viewModels;
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
        $headerTexts  = self::resolveHeaderTexts($cmsContent, $pageSlug);
        $cmsSettings  = self::extractCmsSettings($cmsContent);
        $filterContext = self::resolveFilterContext($scheduleData, $cmsContent, $days, $activeFilters, $availableDays);

        return new ScheduleSectionViewModel(
            sectionId: $pageSlug . '-schedule',
            title: $headerTexts['title'],
            year: $headerTexts['year'],
            eventTypeSlug: $scheduleData['eventTypeSlug'],
            eventTypeId: $scheduleData['eventTypeId'],
            filtersButtonText: $cmsSettings['filtersButtonText'],
            showFilters: $cmsSettings['showFilters'],
            additionalInfoTitle: $cmsSettings['additionalInfoTitle'],
            additionalInfoBody: $cmsSettings['additionalInfoBody'],
            showAdditionalInfo: $cmsSettings['showAdditionalInfo'],
            eventCountLabel: $headerTexts['eventCountLabel'],
            eventCount: $filterContext['eventCount'],
            showEventCount: $headerTexts['showEventCount'],
            ctaButtonText: $cmsSettings['ctaButtonText'],
            payWhatYouLikeText: $cmsSettings['payWhatYouLikeText'],
            currencySymbol: $cmsSettings['currencySymbol'],
            noEventsText: $cmsSettings['noEventsText'],
            days: $days,
            confirmText: $buttonTexts['confirm'],
            addingText: $buttonTexts['adding'],
            successText: $buttonTexts['success'],
            filterGroups: $filterContext['filterGroups'],
            resetButtonText: $filterContext['resetButtonText'],
            hasActiveFilters: $activeFilters !== null && $activeFilters->hasAnyFilter(),
            gridClasses: self::resolveGridClasses(count($days)),
            itemClasses: self::resolveItemClasses(count($days)),
        );
    }

    /**
     * Builds filter groups, counts total events, and resolves the reset button text.
     *
     * @param ScheduleDayViewModel[] $days
     * @param \App\DTOs\Schedule\ScheduleDayData[] $availableDays
     * @return array{eventCount: int, filterGroups: array, resetButtonText: string}
     */
    // Returns associative array — internal mapper output consumed only by toScheduleSection()
    private static function resolveFilterContext(
        array $scheduleData,
        ScheduleSectionContent $cmsContent,
        array $days,
        ?ScheduleFilterParams $activeFilters,
        array $availableDays,
    ): array {
        $eventCount = array_sum(array_map(fn ($day) => count($day->events), $days));
        $filterGroupTypes = $scheduleData['filterGroupTypes'] ?? ['day'];
        $priceTypeOptions = $scheduleData['priceTypeOptions'] ?? ['pay-what-you-like', 'fixed'];
        $filterGroups = ScheduleFilterMapper::buildFilterGroups(
            $cmsContent, $filterGroupTypes, $priceTypeOptions, $days, $activeFilters, $availableDays,
        );
        $resetButtonText = self::str($cmsContent->scheduleFilterResetText, 'Reset all filters');

        return [
            'eventCount'      => $eventCount,
            'filterGroups'    => $filterGroups,
            'resetButtonText' => $resetButtonText,
        ];
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
     * Maps raw day arrays into ScheduleDayViewModel objects.
     *
     * @param array<array<string, mixed>> $rawDays
     * @return ScheduleDayViewModel[]
     */
    private static function mapDays(array $rawDays, string $confirmText, string $addingText, string $successText): array
    {
        $days = [];
        foreach ($rawDays as $day) {
            $days[] = self::mapSingleDay($day, $confirmText, $addingText, $successText);
        }
        return $days;
    }

    /**
     * Maps a single raw day array into a ScheduleDayViewModel.
     *
     * @param array<string, mixed> $day
     */
    private static function mapSingleDay(array $day, string $confirmText, string $addingText, string $successText): ScheduleDayViewModel
    {
        $isoDate = $day['isoDate'];
        $dateObj = new \DateTimeImmutable($isoDate);
        $events  = [];

        foreach ($day['events'] as $event) {
            $events[] = ScheduleCardMapper::toEventCardViewModel($event, $confirmText, $addingText, $successText);
        }

        $dayNumber = $dateObj->format('j');
        $htmlId = 'schedule-day-' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $day['dayName'])) . '-' . $dayNumber;

        return new ScheduleDayViewModel(
            dayName: $day['dayName'],
            dateFormatted: $dateObj->format('l, F j'),
            isoDate: $isoDate,
            events: $events,
            isEmpty: $day['isEmpty'],
            htmlId: $htmlId,
        );
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

    /** Returns a non-empty string value, or the default when null/empty. */
    private static function str(?string $value, string $default): string
    {
        return $value !== null && $value !== '' ? $value : $default;
    }
}
