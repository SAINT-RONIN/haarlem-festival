<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the EventHighlight table.
 *
 * Featured highlights for events, displayed on detail pages.
 */
final readonly class EventHighlight
{
    public function __construct(
        public int    $eventHighlightId,
        public int    $eventId,
        public string $title,
        public string $description,
        public string $imagePath,
        public int    $sortOrder,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            eventHighlightId: (int) ($row['EventHighlightId'] ?? throw new \InvalidArgumentException('Missing required field: EventHighlightId')),
            eventId: (int) ($row['EventId'] ?? throw new \InvalidArgumentException('Missing required field: EventId')),
            title: (string) ($row['Title'] ?? throw new \InvalidArgumentException('Missing required field: Title')),
            description: (string) ($row['Description'] ?? throw new \InvalidArgumentException('Missing required field: Description')),
            imagePath: (string) ($row['ImagePath'] ?? throw new \InvalidArgumentException('Missing required field: ImagePath')),
            sortOrder: (int) ($row['SortOrder'] ?? throw new \InvalidArgumentException('Missing required field: SortOrder')),
        );
    }
}
