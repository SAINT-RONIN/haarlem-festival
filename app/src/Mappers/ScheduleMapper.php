<?php

declare(strict_types=1);

namespace App\Mappers;

use App\ViewModels\Schedule\ScheduleEventCardViewModel;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * Public entry point for schedule mapping. Delegates to ScheduleDayMapper
 * (section/day assembly), ScheduleFilterMapper (filter groups), and
 * ScheduleCardMapper (event card formatting).
 */
final class ScheduleMapper
{
    /**
     * Converts raw schedule data (from ScheduleService::getScheduleData) into a ScheduleSectionViewModel.
     *
     * @param array{cmsContent: \App\Models\ScheduleSectionContent, pageSlug: string, eventTypeSlug: string, eventTypeId: int, days: array} $scheduleData
     */
    public static function toScheduleSection(array $scheduleData): ScheduleSectionViewModel
    {
        return ScheduleDayMapper::buildSection($scheduleData);
    }

    /**
     * Flattens all events from all schedule days into a single array.
     *
     * @return array<array<string, mixed>>
     */
    public static function flattenEvents(array $scheduleData): array
    {
        return ScheduleDayMapper::flattenEvents($scheduleData);
    }

    /**
     * Flattens all events from all schedule days into typed ScheduleEventCardViewModels.
     *
     * @return ScheduleEventCardViewModel[]
     */
    public static function flattenEventsAsViewModels(array $scheduleData): array
    {
        return ScheduleDayMapper::flattenEventsAsViewModels($scheduleData);
    }

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
        return ScheduleCardMapper::toEventCardViewModel($event, $confirmText, $addingText, $successText);
    }
}
