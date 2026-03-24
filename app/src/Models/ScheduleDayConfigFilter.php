<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Typed filter parameters for ScheduleDayConfig repository queries.
 */
final readonly class ScheduleDayConfigFilter
{
    public function __construct(
        public ?int    $eventTypeId = null,
        public ?bool   $includeEventTypeName = null,
        public ?string $orderBy = null,
    ) {
    }
}
