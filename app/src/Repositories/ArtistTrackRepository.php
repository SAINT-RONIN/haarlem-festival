<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\ArtistTrack;
use App\Repositories\Interfaces\IArtistTrackRepository;
use PDO;

/**
 * Read-only access to the ArtistTrack table.
 *
 * Tracks are featured songs for a Jazz artist, shown on the artist detail
 * page in SortOrder (typically embedded Spotify/audio links).
 */
class ArtistTrackRepository implements IArtistTrackRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Returns all tracks for an event, ordered by SortOrder.
     *
     * @return ArtistTrack[]
     */
    public function findByEventId(int $eventId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM ArtistTrack
            WHERE EventId = :eventId
            ORDER BY SortOrder ASC
        ');
        $stmt->execute(['eventId' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([ArtistTrack::class, 'fromRow'], $rows);
    }
}
