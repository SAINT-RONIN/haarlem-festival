<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Filters;

/**
 * Typed filter parameters for Event repository queries.
 *
 * @see \App\Repositories\Interfaces\IEventRepository::findEvents()
 */
final readonly class EventFilter
{
    public function __construct(
        public ?int $eventTypeId = null,
        public ?int $dayOfWeekNumber = null,
        public ?bool $isActive = null,
        public ?bool $includeSessionCount = null,
        public ?int $eventId = null,
    ) {}

    /**
     * @param array<string, mixed> $filters
     */
    public static function fromArray(array $filters): self
    {
        return new self(
            eventTypeId: isset($filters['eventTypeId']) ? (int) $filters['eventTypeId'] : null,
            dayOfWeekNumber: isset($filters['dayOfWeekNumber']) ? (int) $filters['dayOfWeekNumber'] : null,
            isActive: array_key_exists('isActive', $filters) ? (bool) $filters['isActive'] : null,
            includeSessionCount: isset($filters['includeSessionCount']) ? (bool) $filters['includeSessionCount'] : null,
            eventId: isset($filters['eventId']) ? (int) $filters['eventId'] : null,
        );
    }
}
