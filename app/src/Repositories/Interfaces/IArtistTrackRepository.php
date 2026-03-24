<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\ArtistTrack;

/**
 * Defines persistence operations for artist tracks linked to events.
 */
interface IArtistTrackRepository
{
    /**
     * Returns all tracks for an event, ordered by SortOrder.
     *
     * @return ArtistTrack[]
     */
    public function findByEventId(int $eventId): array;
}
