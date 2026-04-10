<?php

declare(strict_types=1);

namespace App\ViewModels\History;

/**
 * Describes a single stop in the History walking route.
 */
final readonly class RouteVenue
{
    public function __construct(
        public string $venueName,
        public string $venueBadgeColor,
        public string $venueDescription,
    ) {}
}
