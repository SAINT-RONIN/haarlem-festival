<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single Storytelling event as returned by the event repository.
 * The reason for this is because the detail service needs only a subset of event fields, so this model captures exactly those fields rather than reusing a generic Event model with unneeded properties.
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
