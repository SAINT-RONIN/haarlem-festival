<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\ArtistLineupMember;

/**
 * Defines persistence operations for artist lineup members linked to artists.
 */
interface IArtistLineupMemberRepository
{
    /**
     * Returns all lineup members for an artist, ordered by SortOrder.
     *
     * @return ArtistLineupMember[]
     */
    public function findByArtistId(int $artistId): array;
}
