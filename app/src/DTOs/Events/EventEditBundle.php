<?php

declare(strict_types=1);

namespace App\DTOs\Events;

/**
 * Typed bundle of data for the event edit page.
 *
 * Returned by CmsEventsService::getEventForEdit() and consumed
 * by CmsEventsController and CmsEventsViewMapper.
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
        public ?string $cmsDetailEditUrl = null,
        public ?string $restaurantStars = null,
        public ?string $restaurantCuisine = null,
        public ?string $restaurantShortDescription = null,
        public ?string $featuredImagePath = null,
    ) {
    }
}
