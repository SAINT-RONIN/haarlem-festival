<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\EventGalleryImage;
use App\Repositories\Interfaces\IEventGalleryImageRepository;
use PDO;

/**
 * Repository for EventGalleryImage database operations.
 */
class EventGalleryImageRepository implements IEventGalleryImageRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Returns gallery images for an event, optionally filtered by image type, ordered by SortOrder.
     *
     * @return EventGalleryImage[]
     */
    public function findByEventId(int $eventId, ?string $imageType = null): array
    {
        try {
            $sql = 'SELECT * FROM EventGalleryImage WHERE EventId = :eventId';
            $params = ['eventId' => $eventId];

            if ($imageType !== null) {
                $sql .= ' AND ImageType = :imageType';
                $params['imageType'] = $imageType;
            }

            $sql .= ' ORDER BY SortOrder ASC';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map([EventGalleryImage::class, 'fromRow'], $rows);
        } catch (\PDOException) {
            return [];
        }
    }
}
