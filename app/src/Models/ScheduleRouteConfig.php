<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Resolved configuration for a schedule API request.
 */
final readonly class ScheduleRouteConfig
{
    public function __construct(
        public string $pageSlug,
        public int $eventTypeId,
        public int $maxDays,
    ) {
    }
}
