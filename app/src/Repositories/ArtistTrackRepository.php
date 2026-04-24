<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ArtistTrack;
use App\Repositories\Interfaces\IArtistTrackRepository;

class ArtistTrackRepository extends BaseRepository implements IArtistTrackRepository
{
    public function findByArtistId(int $artistId): array
    {
        return $this->fetchAll(
            'SELECT * FROM ArtistTrack WHERE ArtistId = :artistId ORDER BY SortOrder ASC',
            ['artistId' => $artistId],
            fn(array $row) => ArtistTrack::fromRow($row),
        );
    }
}
