<?php

declare(strict_types=1);

namespace App\DTOs\Domain\Events;

/**
 * Read-only projection of a restaurant record from the Event table.
 */
final readonly class RestaurantRow
{
    public function __construct(
        public int $eventId,
        public string $slug,
        public string $title,
        public string $shortDescription,
        public string $longDescriptionHtml,
        public ?int $featuredImageAssetId,
        public ?int $venueId,
        public int $stars,
        public int $michelinStars,
        public ?string $cuisineType,
        public float $priceAdult,
        public int $durationMinutes,
        public int $seatsPerSession,
        public ?string $timeSlots,
        public ?string $venueAddressLine = null,
        public ?string $venueCity = null,
    ) {}

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            eventId: (int) ($row['EventId'] ?? throw new \InvalidArgumentException('Missing required field: EventId')),
            slug: (string) ($row['Slug'] ?? throw new \InvalidArgumentException('Missing required field: Slug')),
            title: (string) ($row['Title'] ?? throw new \InvalidArgumentException('Missing required field: Title')),
            shortDescription: (string) ($row['ShortDescription'] ?? ''),
            longDescriptionHtml: (string) ($row['LongDescriptionHtml'] ?? ''),
            featuredImageAssetId: isset($row['FeaturedImageAssetId']) ? (int) $row['FeaturedImageAssetId'] : null,
            venueId: isset($row['VenueId']) ? (int) $row['VenueId'] : null,
            stars: max(0, (int) ($row['Stars'] ?? 0)),
            michelinStars: max(0, (int) ($row['MichelinStars'] ?? 0)),
            cuisineType: $row['CuisineType'] ?? null,
            priceAdult: max(0.0, (float) ($row['PriceAdult'] ?? 0)),
            durationMinutes: max(0, (int) ($row['DurationMinutes'] ?? 0)),
            seatsPerSession: max(0, (int) ($row['SeatsPerSession'] ?? 0)),
            timeSlots: $row['TimeSlots'] ?? null,
            venueAddressLine: $row['VenueAddressLine'] ?? null,
            venueCity: $row['VenueCity'] ?? null,
        );
    }
}
