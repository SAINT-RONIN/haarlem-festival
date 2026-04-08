<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Filters;

/**
 * Query parameters for VenueRepository.
 * Optionally filters to active-only venues.
 */
final readonly class VenueFilter
{
    public function __construct(
        public ?bool $isActive = null,
    ) {}
}
