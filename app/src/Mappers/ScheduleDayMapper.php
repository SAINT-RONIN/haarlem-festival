<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Constants\HistoryPageConstants;
use App\Constants\ScheduleConstants;
use App\DTOs\Cms\ScheduleSectionContent;
use App\DTOs\Domain\Schedule\ScheduleButtonTexts;
use App\DTOs\Domain\Schedule\ScheduleCmsSettings;
use App\DTOs\Domain\Schedule\ScheduleDayPayload;
use App\DTOs\Domain\Schedule\ScheduleFilterContext;
use App\DTOs\Domain\Schedule\ScheduleHeaderTexts;
use App\DTOs\Domain\Schedule\ScheduleSectionData;
use App\ViewModels\Schedule\ScheduleDayViewModel;
use App\ViewModels\Schedule\ScheduleEventCardViewModel;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * Assembles schedule section and day ViewModels from CMS content and raw day arrays,
 * delegating filter groups to ScheduleFilterMapper and event cards to ScheduleCardMapper.
 */
final class ScheduleDayMapper
{
    public static function buildSection(ScheduleSectionData $scheduleData): ScheduleSectionViewModel
    {
        $cmsContent  = $scheduleData->cmsContent;
        $buttonTexts = self::extractButtonTexts($cmsContent);
        $days        = self::mapDays($scheduleData->days, $buttonTexts, $scheduleData);

        return self::buildSectionViewModel($scheduleData, $cmsContent, $days, $buttonTexts);
    }

    /**
     * Flattens all events from all schedule days into raw event card arrays.
     *
     * @return array<array<string, mixed>>
     */
    public static function flattenEvents(ScheduleSectionData $scheduleData): array
    {
        $events = [];
        foreach ($scheduleData->days as $payload) {
            foreach ($payload->sessions as $session) {
                $events[] = ScheduleCardMapper::buildEventCardArray(
                    $session,
                    $scheduleData->eventTypeSlug,
                    $scheduleData->eventTypeId,
                    $payload->labelsMap,
                    $scheduleData->pricesMap,
                    $scheduleData->displayStrings,
                    $payload->historyTourOptions[$session->eventSessionId] ?? [],
                );
            }
        }
        return $events;
    }

    /**
     * Flattens all events from all schedule days into typed ScheduleEventCardViewModels.
     *
     * @return ScheduleEventCardViewModel[]
     */
    public static function flattenEventsAsViewModels(ScheduleSectionData $scheduleData): array
    {
        $viewModels = [];
        foreach ($scheduleData->days as $payload) {
            foreach ($payload->sessions as $session) {
                $cardArray = ScheduleCardMapper::buildEventCardArray(
                    $session,
                    $scheduleData->eventTypeSlug,
                    $scheduleData->eventTypeId,
                    $payload->labelsMap,
                    $scheduleData->pricesMap,
                    $scheduleData->displayStrings,
                    $payload->historyTourOptions[$session->eventSessionId] ?? [],
                );
                $viewModels[] = ScheduleCardMapper::toEventCardViewModel($cardArray, '', '', '');
            }
        }
        return $viewModels;
    }

    private static function extractButtonTexts(ScheduleSectionContent $cmsContent): ScheduleButtonTexts
    {
        return new ScheduleButtonTexts(
            confirm: self::str($cmsContent->scheduleConfirmText, ScheduleConstants::DEFAULT_CONFIRM_TEXT),
            adding: self::str($cmsContent->scheduleAddingText, ScheduleConstants::DEFAULT_ADDING_TEXT),
            success: self::str($cmsContent->scheduleSuccessText, ScheduleConstants::DEFAULT_SUCCESS_TEXT),
        );
    }

    /**
     * Resolves the schedule section header (title, year, event count label).
     * The history page suppresses the year and event count by design.
     */
    private static function resolveHeaderTexts(ScheduleSectionContent $cmsContent, string $pageSlug): ScheduleHeaderTexts
    {
        $title = self::str($cmsContent->scheduleTitle, ucfirst($pageSlug) . ' schedule');
        $year  = self::str($cmsContent->scheduleYear, ScheduleConstants::DEFAULT_SCHEDULE_YEAR);
        $eventCountLabel = self::str(
            $cmsContent->scheduleEventCountLabel,
            self::str($cmsContent->scheduleStoryCountLabel, ScheduleConstants::DEFAULT_EVENT_COUNT_LABEL)
        );
        $showEventCount = ($cmsContent->scheduleShowEventCount
            ?? $cmsContent->scheduleShowStoryCount ?? '1') === '1';

        if ($pageSlug === HistoryPageConstants::PAGE_SLUG) {
            return new ScheduleHeaderTexts(title: $title, year: null, eventCountLabel: null, showEventCount: false);
        }

        return new ScheduleHeaderTexts(
            title: $title,
            year: $year,
            eventCountLabel: $eventCountLabel,
            showEventCount: $showEventCount,
        );
    }

    private static function buildSectionViewModel(
        ScheduleSectionData $scheduleData,
        ScheduleSectionContent $cmsContent,
        array $days,
        ScheduleButtonTexts $buttonTexts,
    ): ScheduleSectionViewModel {
        $pageSlug      = $scheduleData->pageSlug;
        $headerTexts   = self::resolveHeaderTexts($cmsContent, $pageSlug);
        $cmsSettings   = self::extractCmsSettings($cmsContent);
        $filterContext = self::resolveFilterContext($scheduleData, $cmsContent, $days);

        return new ScheduleSectionViewModel(
            sectionId: $pageSlug . '-schedule',
            title: $headerTexts->title,
            year: $headerTexts->year,
            eventTypeSlug: $scheduleData->eventTypeSlug,
            eventTypeId: $scheduleData->eventTypeId,
            filtersButtonText: $cmsSettings->filtersButtonText,
            showFilters: $cmsSettings->showFilters,
            additionalInfoTitle: $cmsSettings->additionalInfoTitle,
            additionalInfoBody: $cmsSettings->additionalInfoBody,
            showAdditionalInfo: $cmsSettings->showAdditionalInfo,
            eventCountLabel: $headerTexts->eventCountLabel,
            eventCount: $filterContext->eventCount,
            showEventCount: $headerTexts->showEventCount,
            ctaButtonText: $cmsSettings->ctaButtonText,
            payWhatYouLikeText: $cmsSettings->payWhatYouLikeText,
            currencySymbol: $cmsSettings->currencySymbol,
            noEventsText: $cmsSettings->noEventsText,
            days: $days,
            confirmText: $buttonTexts->confirm,
            addingText: $buttonTexts->adding,
            successText: $buttonTexts->success,
            filterGroups: $filterContext->filterGroups,
            resetButtonText: $filterContext->resetButtonText,
            hasActiveFilters: $scheduleData->activeFilters !== null && $scheduleData->activeFilters->hasAnyFilter(),
            gridClasses: self::resolveGridClasses(count($days)),
            itemClasses: self::resolveItemClasses(count($days)),
        );
    }

    private static function resolveFilterContext(
        ScheduleSectionData $scheduleData,
        ScheduleSectionContent $cmsContent,
        array $days,
    ): ScheduleFilterContext {
        $eventCount  = array_sum(array_map(fn($day) => count($day->events), $days));
        $filterGroups = ScheduleFilterMapper::buildFilterGroups(
            $cmsContent,
            $scheduleData->filterGroupTypes,
            $scheduleData->priceTypeOptions,
            $days,
            $scheduleData->activeFilters,
            $scheduleData->availableDays,
        );

        return new ScheduleFilterContext(
            eventCount: $eventCount,
            filterGroups: $filterGroups,
            resetButtonText: self::str($cmsContent->scheduleFilterResetText, ScheduleConstants::DEFAULT_RESET_FILTERS_TEXT),
        );
    }

    private static function extractCmsSettings(ScheduleSectionContent $cmsContent): ScheduleCmsSettings
    {
        return new ScheduleCmsSettings(
            filtersButtonText: self::str($cmsContent->scheduleFiltersButtonText, ScheduleConstants::DEFAULT_FILTERS_BUTTON_TEXT),
            showFilters: ($cmsContent->scheduleShowFilters ?? '1') === '1',
            additionalInfoTitle: self::str($cmsContent->scheduleAdditionalInfoTitle, ScheduleConstants::DEFAULT_ADDITIONAL_INFO_TITLE),
            additionalInfoBody: $cmsContent->scheduleAdditionalInfoBody ?? '',
            showAdditionalInfo: ($cmsContent->scheduleShowAdditionalInfo ?? '0') === '1',
            ctaButtonText: self::str($cmsContent->scheduleCtaButtonText, ScheduleConstants::DEFAULT_CTA_BUTTON_TEXT),
            payWhatYouLikeText: self::str($cmsContent->schedulePayWhatYouLikeText, ScheduleConstants::DEFAULT_PAY_WHAT_YOU_LIKE_TEXT),
            currencySymbol: self::str($cmsContent->scheduleCurrencySymbol, ScheduleConstants::DEFAULT_CURRENCY_SYMBOL),
            noEventsText: self::str($cmsContent->scheduleNoEventsText, ScheduleConstants::DEFAULT_NO_EVENTS_TEXT),
        );
    }

    /**
     * @param ScheduleDayPayload[] $payloads
     * @return ScheduleDayViewModel[]
     */
    private static function mapDays(array $payloads, ScheduleButtonTexts $buttonTexts, ScheduleSectionData $scheduleData): array
    {
        $days = [];
        foreach ($payloads as $payload) {
            $days[] = self::mapSingleDay($payload, $buttonTexts, $scheduleData);
        }
        return $days;
    }

    private static function mapSingleDay(ScheduleDayPayload $payload, ScheduleButtonTexts $buttonTexts, ScheduleSectionData $scheduleData): ScheduleDayViewModel
    {
        $events = [];
        foreach ($payload->sessions as $session) {
            $cardArray = ScheduleCardMapper::buildEventCardArray(
                $session,
                $scheduleData->eventTypeSlug,
                $scheduleData->eventTypeId,
                $payload->labelsMap,
                $scheduleData->pricesMap,
                $scheduleData->displayStrings,
                $payload->historyTourOptions[$session->eventSessionId] ?? [],
            );
            $events[] = ScheduleCardMapper::toEventCardViewModel($cardArray, $buttonTexts->confirm, $buttonTexts->adding, $buttonTexts->success);
        }

        $dateObj   = new \DateTimeImmutable($payload->isoDate);
        $dayNumber = $dateObj->format('j');
        $htmlId    = 'schedule-day-' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $payload->dayName)) . '-' . $dayNumber;

        return new ScheduleDayViewModel(
            dayName: $payload->dayName,
            dateFormatted: $dateObj->format('l, F j'),
            isoDate: $payload->isoDate,
            events: $events,
            isEmpty: $payload->isEmpty,
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
