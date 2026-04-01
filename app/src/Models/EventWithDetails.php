<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents an Event row joined with EventType and Venue data.
 *
 * Returned by EventRepository::findEvents() for any query that includes
 * the EventType and Venue joins. Use Event for plain single-row lookups.
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
