<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\ArtistGalleryImage;

/**
 * Defines persistence operations for artist gallery images linked to artists.
 */
interface IArtistGalleryImageRepository
{
    /**
     * Returns all gallery images for an artist, ordered by SortOrder.
     *
     * @return ArtistGalleryImage[]
     */
    public function findByArtistId(int $artistId): array;
}
