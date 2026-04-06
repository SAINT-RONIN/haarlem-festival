<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Filters;

/**
 * Query parameters for ScheduleDayConfigRepository.
 * Filters by event type and/or day of week.
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
