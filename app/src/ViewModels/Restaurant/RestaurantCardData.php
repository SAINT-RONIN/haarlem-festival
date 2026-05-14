<?php

declare(strict_types=1);

namespace App\ViewModels\Restaurant;

/**
 * ViewModel for a single restaurant card on the /restaurant page.
 */
final readonly class RestaurantCardData
{
    /**
     * @param string[] $cuisineTags Lowercased cuisine tags for filtering
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $cuisine,
        public array $cuisineTags,
        public string $address,
        public string $description,
        public int $rating,
        public string $image,
        public ?string $slug = null,
        public bool $isVegan = false,
    ) {}
}
