<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ArtistLineupMember;
use App\Repositories\Interfaces\IArtistLineupMemberRepository;

/**
 * Read-only access to the ArtistLineupMember table.
 *
 * Lineup members represent individual band/ensemble members for a Jazz event,
 * displayed on the artist detail page in SortOrder.
 */
class ArtistLineupMemberRepository extends BaseRepository implements IArtistLineupMemberRepository
{
    /**
     * Returns all lineup members for an event, ordered by SortOrder.
     *
     * @return ArtistLineupMember[]
     */
    public function findByEventId(int $eventId): array
    {
        return $this->fetchAll(
            'SELECT * FROM ArtistLineupMember WHERE EventId = :eventId ORDER BY SortOrder ASC',
            ['eventId' => $eventId],
            fn(array $row) => ArtistLineupMember::fromRow($row),
        );
    }
}
