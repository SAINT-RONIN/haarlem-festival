<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * Typed carrier for event create/update form fields.
 * Constructed by CmsEventsInputMapper and passed through the CMS event service boundary.
 */
final readonly class EventUpsertData
{
    public function __construct(
        public int     $eventTypeId,
        public string  $title,
        public string  $shortDescription,
        public string  $longDescriptionHtml,
        public ?int    $featuredImageAssetId,
        public ?int    $venueId,
        public ?int    $artistId,
        public bool    $isActive,
        public ?string $slug = null,
        public ?int    $restaurantStars = null,
        public ?string $restaurantCuisine = null,
        public ?string $restaurantShortDescription = null,
    ) {}
}
