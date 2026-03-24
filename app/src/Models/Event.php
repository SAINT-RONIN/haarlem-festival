<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a row in the Event table.
 *
 * An event is a bookable activity (e.g., 'Jazz Night at Patronaat') that belongs to one
 * event type and may have multiple sessions across festival days.
 */
final readonly class Event
{
    /*
     * Purpose: Holds event data (title, description, linked venue/artist/restaurant)
     * for festival programming and scheduling.
     */

    public function __construct(
        public int                $eventId,
        public int                $eventTypeId,
        public string             $title,
        public string             $shortDescription,
        public string             $longDescriptionHtml,
        public ?int               $featuredImageAssetId,
        public ?int               $venueId,
        public ?int               $artistId,
        public ?int               $restaurantId,
        public bool               $isActive,
        public \DateTimeImmutable $createdAtUtc,
    ) {
    }

    /**
     * Creates an Event instance from a database row array.
     * Used by repositories after SELECT queries.
     */
    public static function fromRow(array $row): self
    {
        return new self(
            eventId: (int)$row['EventId'],
            eventTypeId: (int)$row['EventTypeId'],
            title: (string)$row['Title'],
            shortDescription: (string)$row['ShortDescription'],
            longDescriptionHtml: (string)$row['LongDescriptionHtml'],
            featuredImageAssetId: isset($row['FeaturedImageAssetId']) ? (int)$row['FeaturedImageAssetId'] : null,
            venueId: isset($row['VenueId']) ? (int)$row['VenueId'] : null,
            artistId: isset($row['ArtistId']) ? (int)$row['ArtistId'] : null,
            restaurantId: isset($row['RestaurantId']) ? (int)$row['RestaurantId'] : null,
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
            'EventId' => $this->eventId,
            'EventTypeId' => $this->eventTypeId,
            'Title' => $this->title,
            'ShortDescription' => $this->shortDescription,
            'LongDescriptionHtml' => $this->longDescriptionHtml,
            'FeaturedImageAssetId' => $this->featuredImageAssetId,
            'VenueId' => $this->venueId,
            'ArtistId' => $this->artistId,
            'RestaurantId' => $this->restaurantId,
            'IsActive' => $this->isActive,
            'CreatedAtUtc' => $this->createdAtUtc->format('Y-m-d H:i:s'),
        ];
    }
}
