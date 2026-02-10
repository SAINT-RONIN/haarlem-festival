<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * DTO for schedule section data.
 */
final readonly class ScheduleData
{
    /**
     * @param ScheduleDayData[] $days
     */
    public function __construct(
        public string $headingText,
        public string $year,
        public string $filterLabel,
        public string $totalEventsText,
        public array $days,
    ) {
    }
}

