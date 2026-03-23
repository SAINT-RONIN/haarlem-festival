<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\ArtistAlbum;

/**
 * Defines persistence operations for artist albums linked to events.
 */
interface IArtistAlbumRepository
{
    /**
     * Returns all albums for an event, ordered by SortOrder.
     *
     * @return ArtistAlbum[]
     */
    public function findByEventId(int $eventId): array;
}
