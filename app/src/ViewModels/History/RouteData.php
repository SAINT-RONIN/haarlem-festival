<?php

declare(strict_types=1);

namespace App\ViewModels\History;

/**
 * ViewModel for the History page route section.
 *
 * Contains the list of locations used for the route list and map.
 */
final readonly class RouteData
{
    /**
     * @param array<int, array{name:string,address:string,category:string,badgeClass:string,lat:float|null,lng:float|null}> $locations
     */
    public function __construct(
        public array $locations = [],
    ) {
    }
}
