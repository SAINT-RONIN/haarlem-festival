<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Query parameters for EventTypeRepository.
 * Optionally filters to a specific event type ID.
 */
final readonly class EventTypeFilter
{
    public function __construct(
        public ?int    $eventTypeId = null,
        public ?string $orderBy = null,
    ) {
    }
}
