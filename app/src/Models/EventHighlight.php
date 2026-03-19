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
            eventHighlightId: (int)$row['EventHighlightId'],
            eventId:          (int)$row['EventId'],
            title:            (string)$row['Title'],
            description:      (string)$row['Description'],
            imagePath:        (string)$row['ImagePath'],
            sortOrder:        (int)$row['SortOrder'],
        );
    }
}
