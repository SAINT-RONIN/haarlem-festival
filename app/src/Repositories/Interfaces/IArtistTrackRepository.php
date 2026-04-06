<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\ArtistTrack;

/**
 * Defines persistence operations for artist tracks linked to artists.
 */
interface IArtistTrackRepository
{
    /**
     * Returns all tracks for an artist, ordered by SortOrder.
     *
     * @return ArtistTrack[]
     */
    public function findByArtistId(int $artistId): array;
}
