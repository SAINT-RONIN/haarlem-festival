<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `ArtistTrack` table.
 */
class ArtistTrack
{
    public function __construct(
        public readonly int    $artistTrackId,
        public readonly int    $eventId,
        public readonly string $title,
        public readonly string $album,
        public readonly string $description,
        public readonly string $duration,
        public readonly string $imagePath,
        public readonly string $progressClass,
        public readonly int    $sortOrder,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            artistTrackId:  (int)$row['ArtistTrackId'],
            eventId:        (int)$row['EventId'],
            title:          (string)$row['Title'],
            album:          (string)$row['Album'],
            description:    (string)$row['Description'],
            duration:       (string)$row['Duration'],
            imagePath:      (string)$row['ImagePath'],
            progressClass:  (string)$row['ProgressClass'],
            sortOrder:      (int)$row['SortOrder'],
        );
    }
}
