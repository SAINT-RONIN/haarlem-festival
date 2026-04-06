<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ArtistGalleryImage;
use App\Repositories\Interfaces\IArtistGalleryImageRepository;

/**
 * Read-only access to the ArtistGalleryImage table.
 *
 * Gallery images are tied to a Jazz artist and shown in the artist detail
 * photo gallery, ordered by SortOrder.
 */
class ArtistGalleryImageRepository extends BaseRepository implements IArtistGalleryImageRepository
{
    /**
     * Returns all gallery images for an artist, ordered by SortOrder.
     *
     * @return ArtistGalleryImage[]
     */
    public function findByArtistId(int $artistId): array
    {
        return $this->fetchAll(
            'SELECT * FROM ArtistGalleryImage WHERE ArtistId = :artistId ORDER BY SortOrder ASC',
            ['artistId' => $artistId],
            fn(array $row) => ArtistGalleryImage::fromRow($row),
        );
    }
}
