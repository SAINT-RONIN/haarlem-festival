<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\ArtistHighlight;

/**
 * Defines persistence operations for artist highlights linked to artists.
 */
interface IArtistHighlightRepository
{
    /**
     * Returns all highlights for an artist, ordered by SortOrder.
     *
     * @return ArtistHighlight[]
     */
    public function findByArtistId(int $artistId): array;
}
