<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ArtistLineupMember;
use App\Repositories\Interfaces\IArtistLineupMemberRepository;

/**
 * Read-only access to the ArtistLineupMember table.
 *
 * Lineup members represent individual band/ensemble members for a Jazz artist,
 * displayed on the artist detail page in SortOrder.
 */
class ArtistLineupMemberRepository extends BaseRepository implements IArtistLineupMemberRepository
{
    /**
     * Returns all lineup members for an artist, ordered by SortOrder.
     *
     * @return ArtistLineupMember[]
     */
    public function findByArtistId(int $artistId): array
    {
        return $this->fetchAll(
            'SELECT * FROM ArtistLineupMember WHERE ArtistId = :artistId ORDER BY SortOrder ASC',
            ['artistId' => $artistId],
            fn(array $row) => ArtistLineupMember::fromRow($row),
        );
    }
}
