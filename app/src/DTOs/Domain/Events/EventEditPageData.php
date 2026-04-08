<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Events;

use App\DTOs\Domain\Schedule\SessionWithEvent;
use App\Models\EventSessionLabel;
use App\Models\EventSessionPrice;

/**
 * Page data for the event edit page.
 *
 * Returned by CmsEventsService::getEventForEdit() and consumed
 * by CmsEventsController and CmsEventsViewMapper.
 *
 * @param SessionWithEvent[]              $sessions
 * @param array<int, EventSessionPrice[]> $pricesMap
 * @param array<int, EventSessionLabel[]> $labelsMap
 */
final readonly class EventEditPageData
{
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
