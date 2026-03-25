<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ArtistLineupMember;
use App\Repositories\Interfaces\IArtistLineupMemberRepository;
use PDO;

/**
 * Read-only access to the ArtistLineupMember table.
 *
 * Lineup members represent individual band/ensemble members for a Jazz event,
 * displayed on the artist detail page in SortOrder.
 */
class ArtistLineupMemberRepository implements IArtistLineupMemberRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Returns all lineup members for an event, ordered by SortOrder.
     *
     * @return ArtistLineupMember[]
     */
    public function findByEventId(int $eventId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM ArtistLineupMember
            WHERE EventId = :eventId
            ORDER BY SortOrder ASC
        ');
        $stmt->execute(['eventId' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([ArtistLineupMember::class, 'fromRow'], $rows);
    }
}
