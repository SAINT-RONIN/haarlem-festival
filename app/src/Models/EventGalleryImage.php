<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `EventGalleryImage` table.
 */
class EventGalleryImage
{
    public function __construct(
        public readonly int    $eventGalleryImageId,
        public readonly int    $eventId,
        public readonly string $imagePath,
        public readonly string $imageType,
        public readonly int    $sortOrder,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            eventGalleryImageId: (int)$row['EventGalleryImageId'],
            eventId:             (int)$row['EventId'],
            imagePath:           (string)$row['ImagePath'],
            imageType:           (string)$row['ImageType'],
            sortOrder:           (int)$row['SortOrder'],
        );
    }
}
