<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Restaurant;

final readonly class RestaurantCmsData
{
    public function __construct(
        public ?string $stars = null,
        public ?string $cuisine = null,
        public ?string $shortDescription = null,
    ) {}
}
