<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Schedule;

/** Resolved schedule section header content (title, year, event count). */
final readonly class ScheduleHeaderTexts
{
    public function __construct(
        public string  $title,
        public ?string $year,
        public ?string $eventCountLabel,
        public bool    $showEventCount,
    ) {}
}
