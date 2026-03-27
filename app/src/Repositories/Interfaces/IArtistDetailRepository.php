<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\DTOs\Events\ArtistDetailBundle;

/**
 * Aggregates all artist sub-entity lookups (albums, tracks, lineup, highlights, gallery)
 * into a single repository call.
 */
interface IArtistDetailRepository
{
    /**
     * Fetches all artist detail data for a given event.
     *
     * @param int $eventId The event whose artist details should be loaded
     */
    public function findByEventId(int $eventId): ArtistDetailBundle;
}
