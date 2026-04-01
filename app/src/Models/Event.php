<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Represents a single row from the `Event` SQL table.
 *
 * Used as a typed data object between PDO/repositories and the rest of the application.
 * Typical flow: SELECT -> fromRow() -> use in service/controller/view -> toArray() -> INSERT/UPDATE.
 */
class Event
{
    /*
     * Purpose: Holds event data (title, description, linked venue/artist/restaurant)
     * for festival programming and scheduling.
     */

    public function __construct(
        public readonly int                $eventId,
        public readonly int                $eventTypeId,
        public readonly string             $title,
        public readonly ?string            $slug,
        public readonly string             $shortDescription,
        public readonly string             $longDescriptionHtml,
        public readonly ?int               $featuredImageAssetId,
        public readonly ?int               $venueId,
        public readonly ?int               $artistId,
        public readonly bool               $isActive,
        public readonly \DateTimeImmutable $createdAtUtc,
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
            slug: isset($row['Slug']) ? (string)$row['Slug'] : null,
            shortDescription: (string)$row['ShortDescription'],
            longDescriptionHtml: (string)$row['LongDescriptionHtml'],
            featuredImageAssetId: isset($row['FeaturedImageAssetId']) ? (int)$row['FeaturedImageAssetId'] : null,
            venueId: isset($row['VenueId']) ? (int)$row['VenueId'] : null,
            artistId: isset($row['ArtistId']) ? (int)$row['ArtistId'] : null,
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
            'Slug' => $this->slug,
            'ShortDescription' => $this->shortDescription,
            'LongDescriptionHtml' => $this->longDescriptionHtml,
            'FeaturedImageAssetId' => $this->featuredImageAssetId,
            'VenueId' => $this->venueId,
            'ArtistId' => $this->artistId,
            'IsActive' => $this->isActive,
            'CreatedAtUtc' => $this->createdAtUtc->format('Y-m-d H:i:s'),
        ];
    }
}
