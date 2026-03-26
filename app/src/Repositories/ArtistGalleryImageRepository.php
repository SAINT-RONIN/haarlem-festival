<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ArtistGalleryImage;
use App\Repositories\Interfaces\IArtistGalleryImageRepository;

/**
 * Read-only access to the ArtistGalleryImage table.
 *
 * Gallery images are tied to a Jazz event and shown in the artist detail
 * photo gallery, ordered by SortOrder.
 */
class ArtistGalleryImageRepository extends BaseRepository implements IArtistGalleryImageRepository
{
    /**
     * Returns all gallery images for an event, ordered by SortOrder.
     *
     * @return ArtistGalleryImage[]
     */
    public function findByEventId(int $eventId): array
    {
        return $this->fetchAll(
            'SELECT * FROM ArtistGalleryImage WHERE EventId = :eventId ORDER BY SortOrder ASC',
            ['eventId' => $eventId],
            fn(array $row) => ArtistGalleryImage::fromRow($row),
        );
    }
}
