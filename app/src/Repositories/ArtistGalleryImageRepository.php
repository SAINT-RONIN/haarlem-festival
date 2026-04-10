<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ArtistGalleryImage;
use App\Repositories\Interfaces\IArtistGalleryImageRepository;

class ArtistGalleryImageRepository extends BaseRepository implements IArtistGalleryImageRepository
{
    public function findByArtistId(int $artistId): array
    {
        return $this->fetchAll(
            'SELECT * FROM ArtistGalleryImage WHERE ArtistId = :artistId ORDER BY SortOrder ASC',
            ['artistId' => $artistId],
            fn(array $row) => ArtistGalleryImage::fromRow($row),
        );
    }
}
