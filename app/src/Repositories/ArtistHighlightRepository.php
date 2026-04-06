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
     * Returns all highlights for an artist, ordered by SortOrder.
     *
     * @return ArtistHighlight[]
     */
    public function findByArtistId(int $artistId): array
    {
        return $this->fetchAll(
            'SELECT * FROM ArtistHighlight WHERE ArtistId = :artistId ORDER BY SortOrder ASC',
            ['artistId' => $artistId],
            fn(array $row) => ArtistHighlight::fromRow($row),
        );
    }
}
