<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Events;

use App\DTOs\Domain\Schedule\SessionWithEvent;
use App\Models\EventSessionLabel;
use App\Models\EventSessionPrice;

/**
 * Typed bundle of data for the event edit page.
 *
 * Returned by CmsEventsService::getEventForEdit() and consumed
 * by CmsEventsController and CmsEventsViewMapper.
 */
final readonly class EventEditBundle
{
    /**
     * @param SessionWithEvent[]              $sessions
     * @param array<int, EventSessionPrice[]> $pricesMap
     * @param array<int, EventSessionLabel[]> $labelsMap
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
