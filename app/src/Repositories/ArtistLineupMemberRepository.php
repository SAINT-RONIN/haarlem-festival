<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\ArtistLineupMember;
use App\Repositories\Interfaces\IArtistLineupMemberRepository;
use PDO;

/**
 * Repository for ArtistLineupMember database operations.
 */
class ArtistLineupMemberRepository implements IArtistLineupMemberRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
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
