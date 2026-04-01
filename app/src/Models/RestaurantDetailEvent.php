<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Thin domain payload for a Restaurant event lookup — mirrors JazzArtistDetailEvent
 * and StorytellingDetailEvent.
 *
 * Only carries columns from the Event table itself. Restaurant-specific content
 * (address, phone, chef, menu, etc.) lives in per-event CMS sections and is
 * carried by RestaurantEventCmsData.
 */
final readonly class RestaurantDetailEvent
{
    public function __construct(
        public int    $eventId,
        public string $slug,
        public string $title,
        public string $shortDescription,
        public string $longDescriptionHtml,
        public ?int   $featuredImageAssetId,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            eventId:             (int)($row['EventId']             ?? throw new \InvalidArgumentException('Missing EventId')),
            slug:                (string)($row['Slug']             ?? throw new \InvalidArgumentException('Missing Slug')),
            title:               (string)($row['Title']            ?? ''),
            shortDescription:    (string)($row['ShortDescription'] ?? ''),
            longDescriptionHtml: (string)($row['LongDescriptionHtml'] ?? ''),
            featuredImageAssetId: isset($row['FeaturedImageAssetId']) ? (int)$row['FeaturedImageAssetId'] : null,
        );
    }
}