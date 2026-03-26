<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `Restaurant` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
final readonly class Restaurant
{
    /*
     * Purpose: Stores restaurant information for festival dining events
     * including location, cuisine type, and star rating.
     */

    public function __construct(
        public int                $restaurantId,
        public string             $name,
        public string             $addressLine,
        public string             $city,
        public ?int               $stars,
        public string             $cuisineType,
        public string             $descriptionHtml,
        public ?int               $imageAssetId,
        public bool               $isActive,
        public \DateTimeImmutable $createdAtUtc,
        // Image path from MediaAsset table (filled by JOIN in repository).
        // null when the restaurant has no linked image.
        public ?string            $imagePath = null,

        // --- Detail page fields (added by migration v29) ---
        // Contact info
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $website = null,
        // About section
        public ?string $aboutText = null,
        // Chef section
        public ?string $chefName = null,
        public ?string $chefText = null,
        // Menu section
        public ?string $menuDescription = null,
        // Location section
        public ?string $locationDescription = null,
        public ?string $mapEmbedUrl = null,
        // Practical info
        public ?int    $michelinStars = null,
        public ?int    $seatsPerSession = null,
        public ?int    $durationMinutes = null,
        public ?string $specialRequestsNote = null,
    ) {
    }

    /**
     * Creates a Restaurant instance from a database row array.
     * Used by repositories after SELECT queries.
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
        );
    }

    /**
     * Converts the model to an associative array for INSERT/UPDATE queries.
     * Keys match the database column names.
     *
     * @return array<string, mixed>
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
        ];
    }
}
