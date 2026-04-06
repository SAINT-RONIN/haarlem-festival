<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * A single day tab in the homepage schedule preview.
 */
final readonly class HomeScheduleDayViewModel
{
    /**
     * @param HomeScheduleSessionViewModel[] $sessions
     */
    public function __construct(
        public string $date,
        public string $dayName,
        public string $dayNumber,
        public string $monthShort,
        public string $isoDate,
        public int $eventCount,
        public array $sessions,
        public string $htmlId = '',
    ) {
    }
}
