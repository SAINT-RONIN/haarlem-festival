<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the ArtistLineupMember table.
 *
 * Band/ensemble members shown on jazz artist detail pages.
 */
final readonly class ArtistLineupMember
{
    public function __construct(
        public int    $artistLineupMemberId,
        public int    $eventId,
        public string $memberText,
        public int    $sortOrder,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            artistLineupMemberId: (int)$row['ArtistLineupMemberId'],
            eventId:              (int)$row['EventId'],
            memberText:           (string)$row['MemberText'],
            sortOrder:            (int)$row['SortOrder'],
        );
    }
}
