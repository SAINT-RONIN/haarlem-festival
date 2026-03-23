<?php

declare(strict_types=1);

namespace App\ViewModels\Schedule;

/**
 * Generic ViewModel for a schedule day column, works for all event types.
 */
final readonly class ScheduleDayViewModel
{
    /**
     * @param string $dayName Day name (e.g., "Thursday")
     * @param string $dateFormatted Formatted date (e.g., "Thursday, July 23")
     * @param string $isoDate ISO date (e.g., "2026-07-23")
     * @param array<ScheduleEventCardViewModel> $events Array of event cards
     * @param bool $isEmpty Whether this day has no events
     */
    public function __construct(
        public string $dayName,
        public string $dateFormatted,
        public string $isoDate,
        public array  $events,
        public bool   $isEmpty = false,
    ) {
    }
}
