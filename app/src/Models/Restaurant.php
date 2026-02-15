<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `Restaurant` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class Restaurant
{
    /*
     * Purpose: Stores restaurant information for festival dining events
     * including location, cuisine type, and star rating.
     */

    public function __construct(
        public readonly int                $restaurantId,
        public readonly string             $name,
        public readonly string             $addressLine,
        public readonly string             $city,
        public readonly ?int               $stars,
        public readonly string             $cuisineType,
        public readonly string             $descriptionHtml,
        public readonly ?int               $imageAssetId,
        public readonly bool               $isActive,
        public readonly \DateTimeImmutable $createdAtUtc,
    )
    {
    }

    /**
     * Creates a Restaurant instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            restaurantId: (int)$row['RestaurantId'],
            name: (string)$row['Name'],
            addressLine: (string)$row['AddressLine'],
            city: (string)$row['City'],
            stars: isset($row['Stars']) ? (int)$row['Stars'] : null,
            cuisineType: (string)$row['CuisineType'],
            descriptionHtml: (string)$row['DescriptionHtml'],
            imageAssetId: isset($row['ImageAssetId']) ? (int)$row['ImageAssetId'] : null,
            isActive: (bool)$row['IsActive'],
            createdAtUtc: new \DateTimeImmutable($row['CreatedAtUtc']),
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     */
    public function toArray(): array
    {
        return [
            'RestaurantId' => $this->restaurantId,
            'Name' => $this->name,
            'AddressLine' => $this->addressLine,
            'City' => $this->city,
            'Stars' => $this->stars,
            'CuisineType' => $this->cuisineType,
            'DescriptionHtml' => $this->descriptionHtml,
            'ImageAssetId' => $this->imageAssetId,
            'IsActive' => $this->isActive,
            'CreatedAtUtc' => $this->createdAtUtc->format('Y-m-d H:i:s'),
        ];
    }
}
