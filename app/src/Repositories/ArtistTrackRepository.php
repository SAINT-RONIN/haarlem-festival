<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ArtistTrack;
use App\Repositories\Interfaces\IArtistTrackRepository;

/**
 * Read-only access to the ArtistTrack table.
 *
 * Tracks are featured songs for a Jazz artist, shown on the artist detail
 * page in SortOrder (typically embedded Spotify/audio links).
 */
class ArtistTrackRepository extends BaseRepository implements IArtistTrackRepository
{
    /**
     * Returns all tracks for an artist, ordered by SortOrder.
     *
     * @return ArtistTrack[]
     */
    public function findByArtistId(int $artistId): array
    {
        return $this->fetchAll(
            'SELECT * FROM ArtistTrack WHERE ArtistId = :artistId ORDER BY SortOrder ASC',
            ['artistId' => $artistId],
            fn(array $row) => ArtistTrack::fromRow($row),
        );
    }
}
