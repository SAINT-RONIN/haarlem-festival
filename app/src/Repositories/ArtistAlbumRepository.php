<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ArtistAlbum;
use App\Repositories\Interfaces\IArtistAlbumRepository;
use PDO;

/**
 * Read-only access to the ArtistAlbum table.
 *
 * Albums are linked to a Jazz event and displayed on the artist detail page
 * in the order defined by SortOrder.
 */
class ArtistAlbumRepository implements IArtistAlbumRepository
{
    public function __construct(private readonly PDO $pdo)
    {
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
