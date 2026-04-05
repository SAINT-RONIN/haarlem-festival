<?php

declare(strict_types=1);

namespace App\Helpers;

use App\DTOs\Cms\EventDetailCmsConfig;

/**
 * Returns the CMS detail-page config for event types that have per-event CMS sections.
 * Returns null for event types that do not (Jazz, Dance, History).
 */
final class EventDetailCmsHelper
{
    public static function forEventType(int $eventTypeId): ?EventDetailCmsConfig
    {
        return match ($eventTypeId) {
            4 => new EventDetailCmsConfig(
                detailPageSlug: 'storytelling-detail',
                sectionKeyPrefix: 'event_',
                supportsPerEventCms: true,
            ),
            5 => new EventDetailCmsConfig(
                detailPageSlug: 'restaurant-detail',
                sectionKeyPrefix: 'event_',
                supportsPerEventCms: true,
            ),
            default => null,
        };
    }

    private function __construct() {}
}
