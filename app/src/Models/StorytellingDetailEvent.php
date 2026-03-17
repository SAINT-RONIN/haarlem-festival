<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Domain payload for Storytelling event detail lookups.
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
