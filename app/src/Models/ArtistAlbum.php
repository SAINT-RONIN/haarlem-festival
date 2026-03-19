<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `ArtistAlbum` table.
 */
class ArtistAlbum
{
    public function __construct(
        public readonly int    $artistAlbumId,
        public readonly int    $eventId,
        public readonly string $title,
        public readonly string $description,
        public readonly string $year,
        public readonly string $tag,
        public readonly string $imagePath,
        public readonly int    $sortOrder,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            artistAlbumId: (int)$row['ArtistAlbumId'],
            eventId:       (int)$row['EventId'],
            title:         (string)$row['Title'],
            description:   (string)$row['Description'],
            year:          (string)$row['Year'],
            tag:           (string)$row['Tag'],
            imagePath:     (string)$row['ImagePath'],
            sortOrder:     (int)$row['SortOrder'],
        );
    }
}
