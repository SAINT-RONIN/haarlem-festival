<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\DTOs\Domain\Events\ArtistDetailBundle;

/**
 * Aggregates all artist sub-entity lookups (albums, tracks, lineup, highlights, gallery)
 * into a single repository call.
 */
interface IArtistDetailRepository
{
    /**
     * Fetches all artist detail data for a given artist.
     *
     * @param int $artistId The artist whose detail collections should be loaded
     */
    public function findByArtistId(int $artistId): ArtistDetailBundle;
}
