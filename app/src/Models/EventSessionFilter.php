<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Typed filter parameters for EventSession repository queries.
 *
 * @see \App\Repositories\Interfaces\IEventSessionRepository::findSessions()
 */
final readonly class EventSessionFilter
{
    /**
     * @param int[]|null $sessionIds
     * @param int[]|null $visibleDays
     */
    public function __construct(
        public ?int $eventId = null,
        public ?int $eventTypeId = null,
        public ?int $sessionId = null,
        public ?array $sessionIds = null,
        public ?bool $isActive = null,
        public ?bool $includeCancelled = null,
        public ?bool $eventIsActive = null,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public ?string $dayOfWeek = null,
        public ?array $visibleDays = null,
        public ?bool $groupByDay = null,
        public ?int $maxDays = null,
        public ?string $orderBy = null,
    ) {
    }

    /**
     * Creates a filter from a legacy array for backward compatibility.
     *
     * @param array<string, mixed> $filters
     */
    public static function fromArray(array $filters): self
    {
        return new self(
            eventId: isset($filters['eventId']) ? (int) $filters['eventId'] : null,
            eventTypeId: isset($filters['eventTypeId']) ? (int) $filters['eventTypeId'] : null,
            sessionId: isset($filters['sessionId']) ? (int) $filters['sessionId'] : null,
            sessionIds: isset($filters['sessionIds']) && is_array($filters['sessionIds']) ? $filters['sessionIds'] : null,
            isActive: array_key_exists('isActive', $filters) ? (bool) $filters['isActive'] : null,
            includeCancelled: isset($filters['includeCancelled']) ? (bool) $filters['includeCancelled'] : null,
            eventIsActive: isset($filters['eventIsActive']) ? (bool) $filters['eventIsActive'] : null,
            startDate: isset($filters['startDate']) && is_string($filters['startDate']) && $filters['startDate'] !== '' ? $filters['startDate'] : null,
            endDate: isset($filters['endDate']) && is_string($filters['endDate']) && $filters['endDate'] !== '' ? $filters['endDate'] : null,
            dayOfWeek: isset($filters['dayOfWeek']) && is_string($filters['dayOfWeek']) && $filters['dayOfWeek'] !== '' ? $filters['dayOfWeek'] : null,
            visibleDays: isset($filters['visibleDays']) && is_array($filters['visibleDays']) ? $filters['visibleDays'] : null,
            groupByDay: isset($filters['groupByDay']) ? (bool) $filters['groupByDay'] : null,
            maxDays: isset($filters['maxDays']) ? (int) $filters['maxDays'] : null,
            orderBy: isset($filters['orderBy']) && is_string($filters['orderBy']) ? $filters['orderBy'] : null,
        );
    }
}
