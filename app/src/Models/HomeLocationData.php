<?php

declare(strict_types=1);

namespace App\Models;

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
