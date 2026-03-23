<?php

declare(strict_types=1);

namespace App\ViewModels\History;

/**
 * View model for the History route section containing the ordered
 * list of route venues and the associated map image.
 */
final readonly class RouteData
{
    /**
     * @param RouteVenue[] $venues
     */
    public function __construct(
        public string $headingText,
        public array $venues = [],
        public string $mapImagePath = '',
    ) {
    }
}
