<?php

declare(strict_types=1);

namespace App\Helpers;

use App\Enums\DayOfWeek;
use App\DTOs\Filters\ScheduleDayConfigFilter;
use App\Repositories\Interfaces\IScheduleDayConfigRepository;

/**
 * Determines which days of the week are visible for a given event type by merging
 * global defaults with type-specific overrides. Type settings take precedence.
 *
 * Shared between CmsEventsService (admin schedule-day management) and ScheduleService
 * (public schedule rendering) to avoid duplicating the two-layer merge logic.
 */
final class ScheduleDayVisibilityResolver
{
    public function __construct(
        private readonly IScheduleDayConfigRepository $scheduleDayConfigRepository,
    ) {
    }

    /**
     * Returns day numbers that should be shown for the given event type.
     *
     * @return int[] Day numbers (0=Sunday through 6=Saturday) that are visible
     */
    public function getVisibleDays(?int $eventTypeId = null): array
    {
        $globalSettings = $this->loadGlobalDaySettings();
        $typeSettings = $this->loadTypeDaySettings($eventTypeId);

        return $this->mergeVisibilitySettings($globalSettings, $typeSettings);
    }

    /**
     * @return array<int, bool> Keyed by day-of-week number
     */
    private function loadGlobalDaySettings(): array
    {
        $settings = [];
        foreach ($this->scheduleDayConfigRepository->findConfigs(new ScheduleDayConfigFilter(eventTypeId: 0, orderBy: 'day')) as $row) {
            $settings[$row->dayOfWeek] = $row->isVisible;
        }
        return $settings;
    }

    /**
     * @return array<int, bool> Keyed by day-of-week number
     */
    private function loadTypeDaySettings(?int $eventTypeId): array
    {
        if ($eventTypeId === null) {
            return [];
        }

        $settings = [];
        foreach ($this->scheduleDayConfigRepository->findConfigs(new ScheduleDayConfigFilter(eventTypeId: $eventTypeId, orderBy: 'day')) as $row) {
            $settings[$row->dayOfWeek] = $row->isVisible;
        }
        return $settings;
    }

    /**
     * Merges global and type-specific settings. Type-specific overrides take precedence.
     * Days without any configuration default to visible.
     *
     * @return int[]
     */
    private function mergeVisibilitySettings(array $globalSettings, array $typeSettings): array
    {
        $visibleDays = [];
        foreach (DayOfWeek::cases() as $day) {
            $dayValue = $day->value;
            $isVisible = $typeSettings[$dayValue] ?? $globalSettings[$dayValue] ?? true;
            if ($isVisible) {
                $visibleDays[] = $dayValue;
            }
        }

        return $visibleDays;
    }
}
