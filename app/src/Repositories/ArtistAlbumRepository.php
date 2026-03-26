<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ArtistAlbum;
use App\Repositories\Interfaces\IArtistAlbumRepository;

/**
 * Read-only access to the ArtistAlbum table.
 *
 * Albums are linked to a Jazz event and displayed on the artist detail page
 * in the order defined by SortOrder.
 */
class ArtistAlbumRepository extends BaseRepository implements IArtistAlbumRepository
{
    /**
     * Returns all albums for an event, ordered by SortOrder.
     *
     * @return ArtistAlbum[]
     */
    public function findByEventId(int $eventId): array
    {
        return $this->fetchAll(
            'SELECT * FROM ArtistAlbum WHERE EventId = :eventId ORDER BY SortOrder ASC',
            ['eventId' => $eventId],
            fn(array $row) => ArtistAlbum::fromRow($row),
        );
    }
}
