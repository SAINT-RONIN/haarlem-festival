<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

/**
 * A single hall within a jazz venue — name, capacity indicator, and free/paid status.
 */
final readonly class HallData
{
    public function __construct(
        public string $name,
        public string $description,
        public string $price,
        public string $capacity,
        public bool $isFree = false,
    ) {
    }
}
