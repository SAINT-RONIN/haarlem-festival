<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\ArtistHighlight;
use App\Repositories\Interfaces\IArtistHighlightRepository;
use PDO;

/**
 * Read-only access to the ArtistHighlight table.
 *
 * Highlights are short "fun fact" or career-milestone blurbs displayed on
 * the artist detail page, ordered by SortOrder.
 */
class ArtistHighlightRepository implements IArtistHighlightRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Returns all highlights for an event, ordered by SortOrder.
     *
     * @return ArtistHighlight[]
     */
    public function findByEventId(int $eventId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM ArtistHighlight
            WHERE EventId = :eventId
            ORDER BY SortOrder ASC
        ');
        $stmt->execute(['eventId' => $eventId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([ArtistHighlight::class, 'fromRow'], $rows);
    }
}
