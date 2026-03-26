<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\EventGalleryImage;
use App\Repositories\Interfaces\IEventGalleryImageRepository;

/**
 * Read-only access to the EventGalleryImage table.
 *
 * Stores gallery/carousel images for an event detail page, with an optional
 * ImageType discriminator (e.g. "hero", "gallery") to separate different usages.
 */
class EventGalleryImageRepository extends BaseRepository implements IEventGalleryImageRepository
{
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

        return $this->fetchAll($sql, $params, fn(array $row) => EventGalleryImage::fromRow($row));
    }
}
