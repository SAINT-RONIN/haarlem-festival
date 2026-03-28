<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Schedule\ScheduleSectionData;
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
     * Converts schedule section data into a ScheduleSectionViewModel.
     */
    public static function toScheduleSection(ScheduleSectionData $scheduleData): ScheduleSectionViewModel
    {
        return ScheduleDayMapper::buildSection($scheduleData);
    }

    /**
     * Flattens all events from all schedule days into a single array.
     *
     * @return array<array<string, mixed>>
     */
    public static function flattenEvents(ScheduleSectionData $scheduleData): array
    {
        return ScheduleDayMapper::flattenEvents($scheduleData);
    }

    /**
     * Flattens all events from all schedule days into typed ScheduleEventCardViewModels.
     *
     * @return ScheduleEventCardViewModel[]
     */
    public static function flattenEventsAsViewModels(ScheduleSectionData $scheduleData): array
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
