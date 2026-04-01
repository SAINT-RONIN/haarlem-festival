<?php

declare(strict_types=1);

namespace App\ViewModels\Restaurant;

/**
 * ViewModel for a single restaurant card on the /restaurant page.
 */
final readonly class RestaurantCardData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $cuisine,
        public string $address,
        public string $description,
        public int $rating,
        public string $image,
        public ?string $slug = null,
    ) {
    }
}
