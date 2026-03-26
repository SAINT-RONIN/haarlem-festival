<?php

declare(strict_types=1);

namespace App\Models;

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
