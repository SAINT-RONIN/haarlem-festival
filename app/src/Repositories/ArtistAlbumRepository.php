<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\ArtistAlbum;
use App\Repositories\Interfaces\IArtistAlbumRepository;
use PDO;

/**
 * Repository for ArtistAlbum database operations.
 */
class ArtistAlbumRepository implements IArtistAlbumRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Returns all albums for an event, ordered by SortOrder.
     *
     * @return ArtistAlbum[]
     */
    public function findByEventId(int $eventId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM ArtistAlbum
            WHERE EventId = :eventId
            ORDER BY SortOrder ASC
        ');
        $stmt->execute(['eventId' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([ArtistAlbum::class, 'fromRow'], $rows);
    }
}
