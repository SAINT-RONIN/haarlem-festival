<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Events;

/**
 * Read-only projection for the jazz artist detail page.
 *
 * JOINs Event with Venue to provide event title, slug, venue name, and featured image
 * for a specific jazz artist.
 */
final readonly class JazzArtistDetailEvent
{
    public function __construct(
        public int $eventId,
        public ?int $artistId,
        public string $title,
        public string $shortDescription,
        public string $longDescriptionHtml,
        public string $slug,
        public string $featuredImageUrl,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            eventId: (int)($row['EventId'] ?? 0),
            artistId: isset($row['ArtistId']) ? (int)$row['ArtistId'] : null,
            title: (string)($row['Title'] ?? ''),
            shortDescription: (string)($row['ShortDescription'] ?? ''),
            longDescriptionHtml: (string)($row['LongDescriptionHtml'] ?? ''),
            slug: (string)($row['Slug'] ?? ''),
            featuredImageUrl: (string)($row['FeaturedImageUrl'] ?? ''),
        );
    }
}
