<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\ArtistLineupMember;

interface IArtistLineupMemberRepository
{
    /**
     * Returns all lineup members for an event, ordered by SortOrder.
     *
     * @return ArtistLineupMember[]
     */
    public function findByEventId(int $eventId): array;
}
