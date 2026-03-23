<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `EventGalleryImage` table.
 */
final readonly class EventGalleryImage
{
    public function __construct(
        public int    $eventGalleryImageId,
        public int    $eventId,
        public string $imagePath,
        public string $imageType,
        public int    $sortOrder,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            eventGalleryImageId: (int)($row['EventGalleryImageId'] ?? throw new \InvalidArgumentException('Missing required field: EventGalleryImageId')),
            eventId:             (int)($row['EventId'] ?? throw new \InvalidArgumentException('Missing required field: EventId')),
            imagePath:           (string)($row['ImagePath'] ?? throw new \InvalidArgumentException('Missing required field: ImagePath')),
            imageType:           (string)($row['ImageType'] ?? throw new \InvalidArgumentException('Missing required field: ImageType')),
            sortOrder:           (int)($row['SortOrder'] ?? throw new \InvalidArgumentException('Missing required field: SortOrder')),
        );
    }
}
