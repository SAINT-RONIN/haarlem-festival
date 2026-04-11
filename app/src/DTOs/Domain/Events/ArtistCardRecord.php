<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Events;

/**
 * Flat record representing an artist card fetched from the Event/Artist tables.
 * Used by both the Jazz and Dance overview pages to populate artist grids.
 */
final readonly class ArtistCardRecord
{
    public function __construct(
        public int $artistId,
        public int $eventId,
        public string $eventSlug,
        public string $artistName,
        public string $artistStyle,
        public string $cardDescription,
        public string $imageUrl,
        public int $cardSortOrder,
        public int $performanceCount,
        public ?\DateTimeImmutable $firstPerformanceAt,
        public string $firstPerformanceLocation,
    ) {}

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            artistId: (int) ($row['ArtistId'] ?? 0),
            eventId: (int) ($row['EventId'] ?? 0),
            eventSlug: (string) ($row['Slug'] ?? ''),
            artistName: (string) ($row['ArtistName'] ?? ''),
            artistStyle: (string) ($row['ArtistStyle'] ?? ''),
            cardDescription: (string) ($row['CardDescription'] ?? ''),
            imageUrl: (string) ($row['ImageUrl'] ?? ''),
            cardSortOrder: (int) ($row['CardSortOrder'] ?? 0),
            performanceCount: (int) ($row['PerformanceCount'] ?? 0),
            firstPerformanceAt: isset($row['FirstPerformanceAt']) && $row['FirstPerformanceAt'] !== null
                ? new \DateTimeImmutable((string) $row['FirstPerformanceAt'])
                : null,
            firstPerformanceLocation: (string) ($row['FirstPerformanceLocation'] ?? ''),
        );
    }
}
