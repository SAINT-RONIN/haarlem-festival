<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\EventGalleryImage;

/**
 * Contract for read-only access to gallery/carousel images linked to events.
 * Images have an optional ImageType discriminator (e.g. "hero", "gallery")
 * to separate different usages on the event detail page.
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
