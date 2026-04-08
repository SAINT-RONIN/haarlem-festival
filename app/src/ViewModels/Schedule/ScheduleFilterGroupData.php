<?php

declare(strict_types=1);

namespace App\ViewModels\Schedule;

/**
 * Represents a single filter category (e.g., Day, Time Range, Venue) in the schedule filter panel.
 */
final readonly class ScheduleFilterGroupData
{
    /**
     * @param ScheduleFilterOptionData[] $options
     */
    public function __construct(
        public string $label,
        public string $key,
        public array $options,
    ) {}
}
