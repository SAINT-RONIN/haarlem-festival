<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\ArtistGalleryImage;

/**
 * Defines persistence operations for artist gallery images linked to events.
 */
interface IArtistGalleryImageRepository
{
    /**
     * Returns all gallery images for an event, ordered by SortOrder.
     *
     * @return ArtistGalleryImage[]
     */
    public function findByEventId(int $eventId): array;
}
