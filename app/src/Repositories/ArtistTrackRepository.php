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
     * Returns all tracks for an event, ordered by SortOrder.
     *
     * @return ArtistTrack[]
     */
    public function findByEventId(int $eventId): array
    {
        return $this->fetchAll(
            'SELECT * FROM ArtistTrack WHERE EventId = :eventId ORDER BY SortOrder ASC',
            ['eventId' => $eventId],
            fn(array $row) => ArtistTrack::fromRow($row),
        );
    }
}
