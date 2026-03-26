<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `RestaurantImage` table.
 *
 * ImageType identifies which section the image belongs to:
 * 'about', 'chef', 'menu', 'gallery', 'reservation'
 */
class RestaurantImage
{
    public function __construct(
        public readonly int    $restaurantImageId,
        public readonly int    $restaurantId,
        public readonly string $imagePath,
        public readonly string $imageType,
        public readonly int    $sortOrder,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            restaurantImageId: (int)$row['RestaurantImageId'],
            restaurantId:      (int)$row['RestaurantId'],
            imagePath:         (string)$row['ImagePath'],
            imageType:         (string)$row['ImageType'],
            sortOrder:         (int)$row['SortOrder'],
        );
    }
}