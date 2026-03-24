<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\EventGalleryImage;
use App\Repositories\Interfaces\IEventGalleryImageRepository;
use PDO;

/**
 * Read-only access to the EventGalleryImage table.
 *
 * Stores gallery/carousel images for an event detail page, with an optional
 * ImageType discriminator (e.g. "hero", "gallery") to separate different usages.
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
    }
}
