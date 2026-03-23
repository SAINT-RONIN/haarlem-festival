<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `ArtistHighlight` table.
 */
final readonly class ArtistHighlight
{
    public function __construct(
        public int    $artistHighlightId,
        public int    $eventId,
        public string $highlightText,
        public int    $sortOrder,
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
