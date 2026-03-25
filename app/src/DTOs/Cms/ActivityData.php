<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * A single CMS activity log entry (e.g., 'Page updated', 'Event created')
 * for the dashboard's recent activity feed.
 */
final readonly class ActivityData
{
    public function __construct(
        public string $icon,
        public string $text,
        public string $time,
        public string $color,
    ) {}
}
