<?php

declare(strict_types=1);

namespace App\DTOs\Pages;

use App\Content\RestaurantEventCmsData;
use App\DTOs\Events\RestaurantDetailEvent;
use App\Models\Restaurant;

/**
 * Intermediate data holder for a single restaurant card on the listing page.
 *
 * Bundles the thin event record, its per-event CMS content, and the resolved
 * featured image path so the mapper can build RestaurantCardData without
 * performing any additional lookups.
 */
final readonly class RestaurantListingData
{
    public function __construct(
        public RestaurantDetailEvent $event,
        public RestaurantEventCmsData $cms,
        public ?string $imagePath,
        public ?Restaurant $restaurant = null,
    ) {
    }
}
