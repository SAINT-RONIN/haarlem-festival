<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * A single day in the jazz page schedule section — date label and event cards.
 */
final readonly class ScheduleDayData
{
    /**
     * @param ScheduleEventData[] $events
     */
    public function __construct(
        public string $dayName,
        public string $fullDate,
        public array $events,
    ) {
    }
}
