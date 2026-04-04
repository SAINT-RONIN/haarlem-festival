<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\ArtistAlbum;

/**
 * Defines persistence operations for artist albums linked to artists.
 */
interface IArtistAlbumRepository
{
    /**
     * Returns all albums for an artist, ordered by SortOrder.
     *
     * @return ArtistAlbum[]
     */
    public function findByArtistId(int $artistId): array;
}
