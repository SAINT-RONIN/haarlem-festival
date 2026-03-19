<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `EventHighlight` table.
 */
class EventHighlight
{
    public function __construct(
        public readonly int    $eventHighlightId,
        public readonly int    $eventId,
        public readonly string $title,
        public readonly string $description,
        public readonly string $imagePath,
        public readonly int    $sortOrder,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            eventHighlightId: (int)($row['EventHighlightId'] ?? throw new \InvalidArgumentException('Missing required field: EventHighlightId')),
            eventId:          (int)($row['EventId'] ?? throw new \InvalidArgumentException('Missing required field: EventId')),
            title:            (string)($row['Title'] ?? throw new \InvalidArgumentException('Missing required field: Title')),
            description:      (string)($row['Description'] ?? throw new \InvalidArgumentException('Missing required field: Description')),
            imagePath:        (string)($row['ImagePath'] ?? throw new \InvalidArgumentException('Missing required field: ImagePath')),
            sortOrder:        (int)($row['SortOrder'] ?? throw new \InvalidArgumentException('Missing required field: SortOrder')),
        );
    }
}
