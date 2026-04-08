<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Schedule;

/** Resolved filter state used to build the schedule section ViewModel. */
final readonly class ScheduleFilterContext
{
    public function __construct(
        public int    $eventCount,
        public array  $filterGroups,
        public string $resetButtonText,
    ) {}
}
