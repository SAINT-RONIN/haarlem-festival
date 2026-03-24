<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\ArtistGalleryImage;
use App\Repositories\Interfaces\IArtistGalleryImageRepository;
use PDO;

/**
 * Read-only access to the ArtistGalleryImage table.
 *
 * Gallery images are tied to a Jazz event and shown in the artist detail
 * photo gallery, ordered by SortOrder.
 */
class ArtistGalleryImageRepository implements IArtistGalleryImageRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Returns all gallery images for an event, ordered by SortOrder.
     *
     * @return ArtistGalleryImage[]
     */
    public function findByEventId(int $eventId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM ArtistGalleryImage
            WHERE EventId = :eventId
            ORDER BY SortOrder ASC
        ');
        $stmt->execute(['eventId' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([ArtistGalleryImage::class, 'fromRow'], $rows);
    }
}
