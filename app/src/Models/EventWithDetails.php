<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Read-only projection from a JOIN across Event, EventType, and Venue.
 *
 * Provides the CMS event list and public pages with combined display data without
 * separate queries per table.
 */
final readonly class EventWithDetails
{
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
        public string             $eventTypeName,
        public string             $eventTypeSlug,
        public ?string            $venueName,
        public int                $sessionCount,
        public int                $totalSoldTickets,
        public int                $totalCapacity,
    ) {
    }

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
            eventTypeName: (string)$row['EventTypeName'],
            eventTypeSlug: (string)$row['EventTypeSlug'],
            venueName: isset($row['VenueName']) ? (string)$row['VenueName'] : null,
            sessionCount: (int)($row['SessionCount'] ?? 0),
            totalSoldTickets: (int)($row['TotalSoldTickets'] ?? 0),
            totalCapacity: (int)($row['TotalCapacity'] ?? 0),
        );
    }
}
