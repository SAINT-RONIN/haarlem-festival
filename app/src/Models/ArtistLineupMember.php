<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `ArtistLineupMember` table.
 */
class ArtistLineupMember
{
    public function __construct(
        public readonly int    $artistLineupMemberId,
        public readonly int    $eventId,
        public readonly string $memberText,
        public readonly int    $sortOrder,
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
