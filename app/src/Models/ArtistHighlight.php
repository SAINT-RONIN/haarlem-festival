<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the ArtistHighlight table.
 *
 * Notable achievements or quotes displayed on jazz artist detail pages.
 */
final readonly class ArtistHighlight
{
    public function __construct(
        public int    $artistHighlightId,
        public int    $artistId,
        public string $highlightText,
        public int    $sortOrder,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            artistHighlightId: (int)$row['ArtistHighlightId'],
            artistId:          (int)$row['ArtistId'],
            highlightText:     (string)$row['HighlightText'],
            sortOrder:         (int)$row['SortOrder'],
        );
    }
}
