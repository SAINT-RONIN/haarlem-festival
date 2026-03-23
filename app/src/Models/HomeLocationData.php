<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Data for a single location pin on the homepage map -- name, coordinates, and category.
 */
final readonly class HomeLocationData
{
    public function __construct(
        public string $name,
        public string $address,
        public string $category,
        public string $badgeClass,
        public ?float $lat,
        public ?float $lng,
    ) {}
}
