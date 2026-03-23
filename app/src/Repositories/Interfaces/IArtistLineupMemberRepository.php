<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\ArtistLineupMember;

/**
 * Defines persistence operations for artist lineup members linked to events.
 */
interface IArtistLineupMemberRepository
{
    /**
     * Returns all lineup members for an event, ordered by SortOrder.
     *
     * @return ArtistLineupMember[]
     */
    public function findByEventId(int $eventId): array;
}
