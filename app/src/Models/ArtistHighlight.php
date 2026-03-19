<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `ArtistHighlight` table.
 */
class ArtistHighlight
{
    public function __construct(
        public readonly int    $artistHighlightId,
        public readonly int    $eventId,
        public readonly string $highlightText,
        public readonly int    $sortOrder,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            artistHighlightId: (int)$row['ArtistHighlightId'],
            eventId:           (int)$row['EventId'],
            highlightText:     (string)$row['HighlightText'],
            sortOrder:         (int)$row['SortOrder'],
        );
    }
}
