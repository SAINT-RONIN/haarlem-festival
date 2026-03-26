<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ArtistHighlight;
use App\Repositories\Interfaces\IArtistHighlightRepository;

/**
 * Read-only access to the ArtistHighlight table.
 *
 * Highlights are short "fun fact" or career-milestone blurbs displayed on
 * the artist detail page, ordered by SortOrder.
 */
class ArtistHighlightRepository extends BaseRepository implements IArtistHighlightRepository
{
    /**
     * Returns all highlights for an event, ordered by SortOrder.
     *
     * @return ArtistHighlight[]
     */
    public function findByEventId(int $eventId): array
    {
        return $this->fetchAll(
            'SELECT * FROM ArtistHighlight WHERE EventId = :eventId ORDER BY SortOrder ASC',
            ['eventId' => $eventId],
            fn(array $row) => ArtistHighlight::fromRow($row),
        );
    }
}
