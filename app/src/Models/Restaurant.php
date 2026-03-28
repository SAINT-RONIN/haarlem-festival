<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `Restaurant` SQL table.
 *
 * Stores core restaurant identity only. Per-event detail content
 * (chef, menu, contact, etc.) lives in RestaurantEventCmsData, edited
 * via the CMS sections interface.
 */
final class Restaurant
{
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
        // Image path from MediaAsset table (filled by JOIN in repository).
        public readonly ?string            $imagePath = null,
    ) {
    }

    /**
     * Creates a Restaurant instance from a database row array.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            restaurantId: (int)($row['RestaurantId'] ?? throw new \InvalidArgumentException('Missing required field: RestaurantId')),
            name: (string)($row['Name'] ?? throw new \InvalidArgumentException('Missing required field: Name')),
            addressLine: (string)($row['AddressLine'] ?? throw new \InvalidArgumentException('Missing required field: AddressLine')),
            city: (string)($row['City'] ?? throw new \InvalidArgumentException('Missing required field: City')),
            stars: isset($row['Stars']) ? (int)$row['Stars'] : null,
            cuisineType: (string)($row['CuisineType'] ?? throw new \InvalidArgumentException('Missing required field: CuisineType')),
            descriptionHtml: (string)($row['DescriptionHtml'] ?? throw new \InvalidArgumentException('Missing required field: DescriptionHtml')),
            imageAssetId: isset($row['ImageAssetId']) ? (int)$row['ImageAssetId'] : null,
            isActive: (bool)($row['IsActive'] ?? throw new \InvalidArgumentException('Missing required field: IsActive')),
            createdAtUtc: new \DateTimeImmutable($row['CreatedAtUtc'] ?? throw new \InvalidArgumentException('Missing required field: CreatedAtUtc')),
            imagePath: isset($row['ImagePath']) ? (string)$row['ImagePath'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'RestaurantId'    => $this->restaurantId,
            'Name'            => $this->name,
            'AddressLine'     => $this->addressLine,
            'City'            => $this->city,
            'Stars'           => $this->stars,
            'CuisineType'     => $this->cuisineType,
            'DescriptionHtml' => $this->descriptionHtml,
            'ImageAssetId'    => $this->imageAssetId,
            'IsActive'        => $this->isActive,
            'CreatedAtUtc'    => $this->createdAtUtc->format('Y-m-d H:i:s'),
        ];
    }
}