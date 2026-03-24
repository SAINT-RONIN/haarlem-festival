<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Typed bundle of data for the event edit page.
 *
 * Returned by CmsEventsService::getEventForEdit() and consumed
 * by CmsEventsController and CmsEventsMapper.
 */
final readonly class EventEditBundle
{
    /**
     * @param SessionWithEvent[] $sessions
     * @param array<int, \App\Models\EventSessionPrice[]> $pricesMap
     * @param array<int, \App\Models\EventSessionLabel[]> $labelsMap
     */
    public function __construct(
        public EventWithDetails $event,
        public array $sessions,
        public array $pricesMap,
        public array $labelsMap,
    ) {
    }
}
