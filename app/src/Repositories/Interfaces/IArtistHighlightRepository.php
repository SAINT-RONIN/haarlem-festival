<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\ArtistHighlight;

interface IArtistHighlightRepository
{
    /**
     * Returns all highlights for an event, ordered by SortOrder.
     *
     * @return ArtistHighlight[]
     */
    public function findByEventId(int $eventId): array;
}
