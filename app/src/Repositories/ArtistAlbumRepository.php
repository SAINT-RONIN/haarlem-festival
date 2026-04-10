<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ArtistAlbum;
use App\Repositories\Interfaces\IArtistAlbumRepository;

class ArtistAlbumRepository extends BaseRepository implements IArtistAlbumRepository
{
    public function findByArtistId(int $artistId): array
    {
        return $this->fetchAll(
            'SELECT * FROM ArtistAlbum WHERE ArtistId = :artistId ORDER BY SortOrder ASC',
            ['artistId' => $artistId],
            fn(array $row) => ArtistAlbum::fromRow($row),
        );
    }
}
