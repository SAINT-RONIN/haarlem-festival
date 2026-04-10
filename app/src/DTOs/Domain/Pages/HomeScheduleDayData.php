<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Pages;

/**
 * Data for a single day in the homepage schedule preview -- date label and session list.
 */
final readonly class HomeScheduleDayData
{
    /**
     * @param HomeScheduleSessionData[] $sessions
     */
    public function __construct(
        public string $date,
        public int $eventCount,
        public array $sessions,
    ) {}
}
