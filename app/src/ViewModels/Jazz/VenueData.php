<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * A single venue block on the jazz page — name, description, image, and hall details.
 */
final readonly class VenueData
{
    /**
     * @param HallData[] $halls
     */
    public function __construct(
        public string $name,
        public string $addressLine1,
        public string $addressLine2,
        public string $contactInfo,
        public array $halls,
        public bool $isDark = false,
    ) {}
}
