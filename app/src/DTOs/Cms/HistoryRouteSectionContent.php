<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * Carries CMS item values for the History route_section.
 */
final readonly class HistoryRouteSectionContent
{
    public function __construct(
        public ?string $routeHeading,
        public ?string $routeMapImage,
        public ?string $routeLocation1Name,
        public ?string $routeLocation1Description,
        public ?string $routeLocation2Name,
        public ?string $routeLocation2Description,
        public ?string $routeLocation3Name,
        public ?string $routeLocation3Description,
        public ?string $routeLocation4Name,
        public ?string $routeLocation4Description,
        public ?string $routeLocation5Name,
        public ?string $routeLocation5Description,
        public ?string $routeLocation6Name,
        public ?string $routeLocation6Description,
        public ?string $routeLocation7Name,
        public ?string $routeLocation7Description,
        public ?string $routeLocation8Name,
        public ?string $routeLocation8Description,
        public ?string $routeLocation9Name,
        public ?string $routeLocation9Description,
    ) {}
}
