<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `RestaurantImage` table.
 *
 * Replaces the 8 numbered image columns on Restaurant with a proper junction table.
 * FilePath is resolved via LEFT JOIN with MediaAsset in the repository.
 */
class RestaurantImage
{
    public function __construct(
        public readonly int     $restaurantImageId,
        public readonly int     $restaurantId,
        public readonly int     $mediaAssetId,
        public readonly string  $imageType,
        public readonly int     $sortOrder,
        // Resolved from JOIN with MediaAsset. null when MediaAsset row is missing.
        public readonly ?string $filePath = null,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            restaurantImageId: (int)$row['RestaurantImageId'],
            restaurantId:      (int)$row['RestaurantId'],
            mediaAssetId:      (int)$row['MediaAssetId'],
            imageType:         (string)$row['ImageType'],
            sortOrder:         (int)$row['SortOrder'],
            filePath:          $row['FilePath'] ?? null,
        );
    }
}
