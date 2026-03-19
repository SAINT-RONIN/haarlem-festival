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
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            eventId: (int)($row['EventId'] ?? 0),
            title: (string)($row['Title'] ?? ''),
            shortDescription: (string)($row['ShortDescription'] ?? ''),
            longDescriptionHtml: (string)($row['LongDescriptionHtml'] ?? ''),
            featuredImageAssetId: isset($row['FeaturedImageAssetId']) ? (int)$row['FeaturedImageAssetId'] : null,
        );
    }
}
