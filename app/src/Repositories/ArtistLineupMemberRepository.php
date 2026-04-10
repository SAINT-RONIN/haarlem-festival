<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ArtistLineupMember;
use App\Repositories\Interfaces\IArtistLineupMemberRepository;

class ArtistLineupMemberRepository extends BaseRepository implements IArtistLineupMemberRepository
{
    public function findByArtistId(int $artistId): array
    {
        return $this->fetchAll(
            'SELECT * FROM ArtistLineupMember WHERE ArtistId = :artistId ORDER BY SortOrder ASC',
            ['artistId' => $artistId],
            fn(array $row) => ArtistLineupMember::fromRow($row),
        );
    }
}
