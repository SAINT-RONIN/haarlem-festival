<?php

declare(strict_types=1);

namespace App\DTOs\Events;

/**
 * Read-only projection for the restaurant detail page.
 *
 * JOINs Event with Restaurant to provide event title, slug, and restaurant FK
 * for a specific restaurant event. Mirrors JazzArtistDetailEvent and
 * StorytellingDetailEvent.
 *
 * restaurantId is included because Event.RestaurantId is an FK needed when
 * persisting Reservation records.
 */
final readonly class RestaurantDetailEvent
{
    public function __construct(
        public int $eventId,
        public ?int $restaurantId,
        public string $slug,
        public string $title,
        public string $shortDescription,
        public string $longDescriptionHtml,
        public ?int $featuredImageAssetId,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            eventId: (int)($row['EventId'] ?? throw new \InvalidArgumentException('Missing required field: EventId')),
            restaurantId: isset($row['RestaurantId']) ? (int)$row['RestaurantId'] : null,
            slug: (string)($row['Slug'] ?? throw new \InvalidArgumentException('Missing required field: Slug')),
            title: (string)($row['Title'] ?? throw new \InvalidArgumentException('Missing required field: Title')),
            shortDescription: (string)($row['ShortDescription'] ?? ''),
            longDescriptionHtml: (string)($row['LongDescriptionHtml'] ?? ''),
            featuredImageAssetId: isset($row['FeaturedImageAssetId']) ? (int)$row['FeaturedImageAssetId'] : null,
        );
    }
}
