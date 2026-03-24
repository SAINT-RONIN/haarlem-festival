<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Typed filter parameters for Venue repository queries.
 */
final readonly class VenueFilter
{
    public function __construct(
        public ?bool $isActive = null,
    ) {
    }
}
