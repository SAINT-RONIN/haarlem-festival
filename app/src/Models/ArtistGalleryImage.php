<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the ArtistGalleryImage table.
 *
 * Photo gallery images for jazz artist detail pages.
 */
final readonly class ArtistGalleryImage
{
    public function __construct(
        public int    $artistGalleryImageId,
        public int    $eventId,
        public string $imagePath,
        public int    $sortOrder,
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
