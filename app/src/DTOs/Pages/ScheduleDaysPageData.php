<?php

declare(strict_types=1);

namespace App\DTOs\Pages;

/**
 * All data needed to render the CMS schedule days page.
 *
 * Assembled by CmsEventsService, consumed by CmsEventsController.
 */
final readonly class ScheduleDaysPageData
{
    /**
     * @param EventType[] $eventTypes
     */
    public function __construct(
        public array $eventTypes,
        public GroupedScheduleDayConfigs $grouped,
    ) {
    }
}
