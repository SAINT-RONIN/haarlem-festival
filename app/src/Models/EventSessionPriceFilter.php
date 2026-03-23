<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Typed filter parameters for EventSessionPrice repository queries.
 *
 * @see \App\Repositories\Interfaces\IEventSessionPriceRepository::findPrices()
 */
final readonly class EventSessionPriceFilter
{
    /**
     * @param int[]|null $sessionIds
     */
    public function __construct(
        public ?int $sessionId = null,
        public ?array $sessionIds = null,
        public ?bool $groupBySession = null,
    ) {
    }

    /**
     * @param array<string, mixed> $filters
     */
    public static function fromArray(array $filters): self
    {
        return new self(
            sessionId: isset($filters['sessionId']) ? (int) $filters['sessionId'] : null,
            sessionIds: isset($filters['sessionIds']) && is_array($filters['sessionIds']) ? $filters['sessionIds'] : null,
            groupBySession: isset($filters['groupBySession']) ? (bool) $filters['groupBySession'] : null,
        );
    }
}
