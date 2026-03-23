<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the ArtistAlbum table.
 *
 * Albums displayed on jazz artist detail pages with cover art and Spotify link.
 */
final readonly class ArtistAlbum
{
    public function __construct(
        public int    $artistAlbumId,
        public int    $eventId,
        public string $title,
        public string $description,
        public string $year,
        public string $tag,
        public string $imagePath,
        public int    $sortOrder,
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
