<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ArtistHighlight;
use App\Repositories\Interfaces\IArtistHighlightRepository;

class ArtistHighlightRepository extends BaseRepository implements IArtistHighlightRepository
{
    public function findByArtistId(int $artistId): array
    {
        return $this->fetchAll(
            'SELECT * FROM ArtistHighlight WHERE ArtistId = :artistId ORDER BY SortOrder ASC',
            ['artistId' => $artistId],
            fn(array $row) => ArtistHighlight::fromRow($row),
        );
    }
}
