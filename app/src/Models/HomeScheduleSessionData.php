<?php

declare(strict_types=1);

namespace App\Models;

final readonly class HomeScheduleSessionData
{
    public function __construct(
        public int $earliestStart,
        public int $latestEnd,
        public string $eventTypeSlug,
        public string $firstEventTitle,
        public string $typeName,
    ) {}
}
