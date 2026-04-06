<?php

declare(strict_types=1);

namespace App\DTOs\Events;

/**
 * Read-only projection for the storytelling detail page.
 *
 * JOINs Event with Venue to provide event title, slug, venue, and featured image
 * for a specific storytelling event.
 */
final readonly class StorytellingDetailEvent
{
    public function __construct(
        public int $eventId,
        public string $title,
        public string $shortDescription,
        public string $longDescriptionHtml,
        public ?int $featuredImageAssetId,
        public string $slug,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            eventId: (int)($row['EventId'] ?? throw new \InvalidArgumentException('Missing required field: EventId')),
            title: (string)($row['Title'] ?? throw new \InvalidArgumentException('Missing required field: Title')),
            shortDescription: (string)($row['ShortDescription'] ?? throw new \InvalidArgumentException('Missing required field: ShortDescription')),
            longDescriptionHtml: (string)($row['LongDescriptionHtml'] ?? throw new \InvalidArgumentException('Missing required field: LongDescriptionHtml')),
            featuredImageAssetId: isset($row['FeaturedImageAssetId']) ? (int)$row['FeaturedImageAssetId'] : null,
            slug: (string)($row['Slug'] ?? throw new \InvalidArgumentException('Missing required field: Slug')),
        );
    }
}
