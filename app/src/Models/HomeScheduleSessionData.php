<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Data for a single session in the homepage schedule preview -- time, title, and venue.
 */
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
