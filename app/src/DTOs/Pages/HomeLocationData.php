<?php

declare(strict_types=1);

namespace App\DTOs\Pages;

/**
 * Data for a single location pin on the homepage map -- name, coordinates, and category.
 */
final readonly class HomeLocationData
{
    public function __construct(
        public string $name,
        public string $address,
        public string $category,
        public ?float $lat,
        public ?float $lng,
    ) {}
}
