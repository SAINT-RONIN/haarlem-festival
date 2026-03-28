<?php

declare(strict_types=1);

namespace App\Models;

final readonly class RestaurantUpsertData
{
    public function __construct(
        public string  $name,
        public string  $addressLine,
        public string  $city,
        public ?int    $stars,
        public string  $cuisineType,
        public string  $descriptionHtml,
        public ?int    $imageAssetId,
        public bool    $isActive,
    ) {}
}