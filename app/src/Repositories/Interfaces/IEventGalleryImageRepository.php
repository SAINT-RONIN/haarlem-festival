<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\EventGalleryImage;

/**
 * Defines persistence operations for gallery images linked to events.
 */
interface IEventGalleryImageRepository
{
    /**
     * Returns gallery images for an event, optionally filtered by image type, ordered by SortOrder.
     *
     * @return EventGalleryImage[]
     */
    public function findByEventId(int $eventId, ?string $imageType = null): array;
}
