<?php

declare(strict_types=1);

namespace App\ViewModels\Schedule;

/**
 * ViewModel for a single day column in the storytelling schedule.
 */
final readonly class StorytellingScheduleDayViewModel
{
    /**
     * @param string $dayName Day name (e.g., "Thursday")
     * @param string $dateFormatted Formatted date (e.g., "Thursday, July 23")
     * @param string $isoDate ISO date for <time> element (e.g., "2026-07-23")
     * @param array<StorytellingScheduleCardViewModel> $events Array of event cards
     */
    public function __construct(
        public string $dayName,
        public string $dateFormatted,
        public string $isoDate,
        public array  $events,
    ) {
    }
}
