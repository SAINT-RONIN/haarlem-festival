<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `ArtistGalleryImage` table.
 */
class ArtistGalleryImage
{
    public function __construct(
        public readonly int    $artistGalleryImageId,
        public readonly int    $eventId,
        public readonly string $imagePath,
        public readonly int    $sortOrder,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            artistGalleryImageId: (int)$row['ArtistGalleryImageId'],
            eventId:              (int)$row['EventId'],
            imagePath:            (string)$row['ImagePath'],
            sortOrder:            (int)$row['SortOrder'],
        );
    }
}
