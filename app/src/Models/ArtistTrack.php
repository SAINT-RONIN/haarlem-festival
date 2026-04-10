<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the ArtistTrack table.
 *
 * Individual tracks shown on jazz artist detail pages with audio preview URLs.
 */
final readonly class ArtistTrack
{
    public function __construct(
        public int    $artistTrackId,
        public int    $artistId,
        public string $title,
        public string $album,
        public string $description,
        public string $duration,
        public string $imagePath,
        public string $progressClass,
        public int    $sortOrder,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            artistTrackId: (int) $row['ArtistTrackId'],
            artistId: (int) $row['ArtistId'],
            title: (string) $row['Title'],
            album: (string) $row['Album'],
            description: (string) $row['Description'],
            duration: (string) $row['Duration'],
            imagePath: (string) $row['ImagePath'],
            progressClass: (string) $row['ProgressClass'],
            sortOrder: (int) $row['SortOrder'],
        );
    }
}
