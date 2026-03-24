<?php

declare(strict_types=1);

namespace App\Models;

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

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        return new self(
            routeHeading: $raw['route_heading'] ?? null,
            routeMapImage: $raw['route_map_image'] ?? null,
            routeLocation1Name: $raw['route_location1_name'] ?? null,
            routeLocation1Description: $raw['route_location1_description'] ?? null,
            routeLocation2Name: $raw['route_location2_name'] ?? null,
            routeLocation2Description: $raw['route_location2_description'] ?? null,
            routeLocation3Name: $raw['route_location3_name'] ?? null,
            routeLocation3Description: $raw['route_location3_description'] ?? null,
            routeLocation4Name: $raw['route_location4_name'] ?? null,
            routeLocation4Description: $raw['route_location4_description'] ?? null,
            routeLocation5Name: $raw['route_location5_name'] ?? null,
            routeLocation5Description: $raw['route_location5_description'] ?? null,
            routeLocation6Name: $raw['route_location6_name'] ?? null,
            routeLocation6Description: $raw['route_location6_description'] ?? null,
            routeLocation7Name: $raw['route_location7_name'] ?? null,
            routeLocation7Description: $raw['route_location7_description'] ?? null,
            routeLocation8Name: $raw['route_location8_name'] ?? null,
            routeLocation8Description: $raw['route_location8_description'] ?? null,
            routeLocation9Name: $raw['route_location9_name'] ?? null,
            routeLocation9Description: $raw['route_location9_description'] ?? null,
        );
    }
}
