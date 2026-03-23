<?php

declare(strict_types=1);

namespace App\ViewModels;

final readonly class HomeLocationViewModel
{
    public function __construct(
        public string $name,
        public string $address,
        public string $category,
        public string $badgeClass,
    ) {
    }
}
