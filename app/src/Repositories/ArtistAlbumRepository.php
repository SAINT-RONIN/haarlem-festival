<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ArtistAlbum;
use App\Repositories\Interfaces\IArtistAlbumRepository;

/**
 * Read-only access to the ArtistAlbum table.
 *
 * Albums are linked to a Jazz artist and displayed on the artist detail page
 * in the order defined by SortOrder.
 */
class ArtistAlbumRepository extends BaseRepository implements IArtistAlbumRepository
{
    /**
     * Returns all albums for an artist, ordered by SortOrder.
     *
     * @return ArtistAlbum[]
     */
    public function findByArtistId(int $artistId): array
    {
        return $this->fetchAll(
            'SELECT * FROM ArtistAlbum WHERE ArtistId = :artistId ORDER BY SortOrder ASC',
            ['artistId' => $artistId],
            fn(array $row) => ArtistAlbum::fromRow($row),
        );
    }
}
