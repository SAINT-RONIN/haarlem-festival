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
        // Image path from MediaAsset table (filled by JOIN in repository).
        // null when the restaurant has no linked image.
        public readonly ?string            $imagePath = null,

        // --- Detail page fields (added by migration v29) ---
        // Contact info
        public readonly ?string $phone = null,
        public readonly ?string $email = null,
        public readonly ?string $website = null,
        // About section
        public readonly ?string $aboutText = null,
        // Chef section
        public readonly ?string $chefName = null,
        public readonly ?string $chefText = null,
        // Menu section
        public readonly ?string $menuDescription = null,
        // Location section
        public readonly ?string $locationDescription = null,
        public readonly ?string $mapEmbedUrl = null,
        // Practical info
        public readonly ?int    $michelinStars = null,
        public readonly ?int    $seatsPerSession = null,
        public readonly ?int    $durationMinutes = null,
        public readonly ?string $specialRequestsNote = null,
        // Available dining time slots (comma-separated, e.g. "16:30, 18:30, 20:30")
        public readonly ?string $timeSlots = null,
        // Menu prices per person
        public readonly ?float  $priceAdult = null,
        public readonly ?float  $priceChild = null,
    ) {
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
            // ImagePath comes from the LEFT JOIN with MediaAsset.
            imagePath: isset($row['ImagePath']) ? (string)$row['ImagePath'] : null,

            // Detail page fields (may be null if columns don't exist yet)
            phone: $row['Phone'] ?? null,
            email: $row['Email'] ?? null,
            website: $row['Website'] ?? null,
            aboutText: $row['AboutText'] ?? null,
            chefName: $row['ChefName'] ?? null,
            chefText: $row['ChefText'] ?? null,
            menuDescription: $row['MenuDescription'] ?? null,
            locationDescription: $row['LocationDescription'] ?? null,
            mapEmbedUrl: $row['MapEmbedUrl'] ?? null,
            michelinStars: isset($row['MichelinStars']) ? (int)$row['MichelinStars'] : null,
            seatsPerSession: isset($row['SeatsPerSession']) ? (int)$row['SeatsPerSession'] : null,
            durationMinutes: isset($row['DurationMinutes']) ? (int)$row['DurationMinutes'] : null,
            specialRequestsNote: $row['SpecialRequestsNote'] ?? null,
            timeSlots:           $row['TimeSlots'] ?? null,
            priceAdult:          isset($row['PriceAdult']) ? (float)$row['PriceAdult'] : null,
            priceChild:          isset($row['PriceChild']) ? (float)$row['PriceChild'] : null,
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
            'Phone' => $this->phone,
            'Email' => $this->email,
            'Website' => $this->website,
            'AboutText' => $this->aboutText,
            'ChefName' => $this->chefName,
            'ChefText' => $this->chefText,
            'MenuDescription' => $this->menuDescription,
            'LocationDescription' => $this->locationDescription,
            'MapEmbedUrl' => $this->mapEmbedUrl,
            'MichelinStars' => $this->michelinStars,
            'SeatsPerSession' => $this->seatsPerSession,
            'DurationMinutes' => $this->durationMinutes,
            'SpecialRequestsNote' => $this->specialRequestsNote,
            'TimeSlots'           => $this->timeSlots,
            'PriceAdult'          => $this->priceAdult,
            'PriceChild'          => $this->priceChild,
        ];
    }
}
