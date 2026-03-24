<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Routing configuration for a schedule page — maps URL slugs to event type IDs
 * and page titles. Used by ScheduleService to determine which events to show.
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
