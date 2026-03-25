<?php

declare(strict_types=1);

namespace App\DTOs\Events;

/**
 * All data needed to render the CMS events list page.
 *
 * Assembled by CmsEventsService, consumed by CmsEventsViewMapper.
 */
final readonly class EventsListPageData
{
    /**
     * @param EventWithDetails[] $events
     * @param EventType[] $eventTypes
     * @param Venue[] $venues
     * @param array<string, SessionWithEvent[]> $weeklySchedule
     */
    public function __construct(
        public array $events,
        public array $eventTypes,
        public array $venues,
        public array $weeklySchedule,
    ) {
    }
}
