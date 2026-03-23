<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * Section data for the jazz page inline schedule — days with event listings.
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
