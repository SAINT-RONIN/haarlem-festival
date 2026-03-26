<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Typed filter parameters for EventType repository queries.
 */
final readonly class EventTypeFilter
{
    public function __construct(
        public ?int    $eventTypeId = null,
        public ?string $orderBy = null,
    ) {
    }
}
