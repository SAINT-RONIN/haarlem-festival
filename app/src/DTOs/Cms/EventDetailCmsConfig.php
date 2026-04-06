<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * Carries CMS detail-page configuration for event types that have per-event CMS sections.
 * Returned by EventDetailCmsHelper::forEventType() and consumed by CmsEventsService.
 */
final readonly class EventDetailCmsConfig
{
    public function __construct(
        public string $detailPageSlug,
        public string $sectionKeyPrefix,
        public bool   $supportsPerEventCms,
    ) {}
}
