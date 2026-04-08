<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

use App\Models\Event;

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

    /**
     * Returns a copy of this DTO with immutable fields sourced from the existing event record.
     *
     * On update, EventTypeId and slug are set at creation and never changed, so they are always
     * taken from the database. ArtistId falls back to the existing value when the form omits it.
     */
    /**
     * Returns a copy of this DTO with the slug set to the given value.
     *
     * Used on the create path after resolveUniqueSlug() produces the final slug,
     * since the slug is not known until after uniqueness is confirmed.
     */
    public function withSlug(string $slug): self
    {
        return new self(
            eventTypeId: $this->eventTypeId,
            title: $this->title,
            shortDescription: $this->shortDescription,
            longDescriptionHtml: $this->longDescriptionHtml,
            featuredImageAssetId: $this->featuredImageAssetId,
            venueId: $this->venueId,
            artistId: $this->artistId,
            isActive: $this->isActive,
            slug: $slug,
            restaurantStars: $this->restaurantStars,
            restaurantCuisine: $this->restaurantCuisine,
            restaurantShortDescription: $this->restaurantShortDescription,
        );
    }

    /**
     * Returns a copy of this DTO with immutable fields sourced from the existing event record.
     *
     * On update, EventTypeId and slug are set at creation and never changed, so they are always
     * taken from the database. ArtistId falls back to the existing value when the form omits it.
     */
    public function forUpdate(Event $existing): self
    {
        return new self(
            eventTypeId: $existing->eventTypeId,
            title: $this->title,
            shortDescription: $this->shortDescription,
            longDescriptionHtml: $this->longDescriptionHtml,
            featuredImageAssetId: $this->featuredImageAssetId,
            venueId: $this->venueId,
            artistId: $this->artistId ?? $existing->artistId,
            isActive: $this->isActive,
            slug: $existing->slug,
        );
    }
}
