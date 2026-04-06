<?php

declare(strict_types=1);

namespace App\ViewModels\History;

/**
 * DTO for single schedule day data.
 */
final readonly class ScheduleDayData
{
    /**
     * @param ScheduleCard[] $events
     */
    public function __construct(
        public string $dayName,
        public string $fullDate,
        public array $events,
    ) {
    }
}
