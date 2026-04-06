<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\EventTypeId;
use App\Models\Event;
use App\DTOs\Cms\EventUpsertData;
use App\DTOs\Filters\EventFilter;
use App\DTOs\Events\EventWithDetails;
use App\DTOs\Events\JazzArtistCardRecord;
use App\DTOs\Events\JazzArtistDetailEvent;
use App\DTOs\Events\RestaurantDetailEvent;
use App\DTOs\Events\StorytellingDetailEvent;
use App\Repositories\Interfaces\IEventRepository;
use PDO;

/**
 * Manages CRUD operations on the Event table, with support for filtered listing
 * that joins Venue, EventType, and aggregated EventSession data.
 * Also provides slug-based lookups for public-facing Jazz and Storytelling detail pages.
 */
class EventRepository extends BaseRepository implements IEventRepository
{
    /**
     * Retrieves events with optional filtering by active status, event type, specific event,
     * and day of week. Joins Venue and EventType for display names. When includeSessionCount
     * is set, attaches aggregate session/ticket counts via a subquery on EventSession.
     *
     * @return EventWithDetails[] Sorted by event type name, then event title. Empty array if no matches.
     */
    public function findEvents(EventFilter $filters = new EventFilter()): array
    {
        $conditions = [];
        $params = [];
        $sql = $this->buildFindEventsQuery($filters, $conditions, $params);

        return $this->fetchAll($sql, $params, fn(array $row) => EventWithDetails::fromRow($row));
    }

    /**
     * @param string[] $conditions
     * @param array<string,mixed> $params
     */
    private function buildFindEventsQuery(EventFilter $filters, array &$conditions, array &$params): string
    {
        $sql = $this->buildEventSelectClause((bool)($filters->includeSessionCount ?? false));
        $sql .= $this->buildEventJoinClause((bool)($filters->includeSessionCount ?? false));
        $sql .= $this->buildDayOfWeekJoinClause($filters->dayOfWeekNumber, $params);
        $this->appendEventConditions($filters, $conditions, $params);

        if ($conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        return $sql . ' ORDER BY et.Name ASC, e.Title ASC';
    }

    private function buildEventSelectClause(bool $includeSessionCount): string
    {
        $select = '
            SELECT DISTINCT
                e.*,
                v.Name AS VenueName,
                et.Name AS EventTypeName,
                et.Slug AS EventTypeSlug
        ';

        if ($includeSessionCount) {
            $select .= ', COALESCE(es_count.SessionCount, 0) AS SessionCount, COALESCE(es_count.TotalSoldTickets, 0) AS TotalSoldTickets, COALESCE(es_count.TotalCapacity, 0) AS TotalCapacity';
        }

        return $select;
    }

    private function buildEventJoinClause(bool $includeSessionCount): string
    {
        $joins = '
            FROM Event e
            LEFT JOIN Venue v ON e.VenueId = v.VenueId
            INNER JOIN EventType et ON e.EventTypeId = et.EventTypeId
        ';

        if (!$includeSessionCount) {
            return $joins;
        }

        return $joins . '
            LEFT JOIN (
                SELECT EventId,
                       COUNT(*) AS SessionCount,
                       COALESCE(SUM(SoldSingleTickets + SoldReservedSeats), 0) AS TotalSoldTickets,
                       COALESCE(SUM(CapacityTotal), 0) AS TotalCapacity
                FROM EventSession
                GROUP BY EventId
            ) es_count ON es_count.EventId = e.EventId
        ';
    }

    /**
     * @param array<string,mixed> $params
     */
    private function buildDayOfWeekJoinClause(?int $dayOfWeekNumber, array &$params): string
    {
        if ($dayOfWeekNumber === null) {
            return '';
        }

        $params['dayOfWeekNum'] = $dayOfWeekNumber;

        return '
            INNER JOIN EventSession es_day
                ON es_day.EventId = e.EventId
                AND DAYOFWEEK(es_day.StartDateTime) = :dayOfWeekNum
        ';
    }

    /**
     * @param string[] $conditions
     * @param array<string,mixed> $params
     */
    private function appendEventConditions(EventFilter $filters, array &$conditions, array &$params): void
    {
        if ($filters->isActive !== null) {
            $conditions[] = 'e.IsActive = :isActive';
            $params['isActive'] = $filters->isActive ? 1 : 0;
        }

        if ($filters->eventTypeId !== null) {
            $conditions[] = 'e.EventTypeId = :eventTypeId';
            $params['eventTypeId'] = (int)$filters->eventTypeId;
        }

        if ($filters->eventId !== null) {
            $conditions[] = 'e.EventId = :eventId';
            $params['eventId'] = (int)$filters->eventId;
        }
    }

    /**
     * Looks up a single active event by its URL slug and event type.
     *
     * @return array<string, mixed>|null Raw row data, or null if not found.
     */
    private function queryActiveEventBySlug(string $slug, EventTypeId $eventType): ?array
    {
        $stmt = $this->execute(
            'SELECT e.*, featured_image.FilePath AS FeaturedImageUrl
            FROM Event e
            LEFT JOIN MediaAsset featured_image ON featured_image.MediaAssetId = e.FeaturedImageAssetId
            WHERE e.EventTypeId = :eventTypeId
              AND e.IsActive = 1
              AND e.Slug = :slug
            LIMIT 1',
            ['eventTypeId' => $eventType->value, 'slug' => $slug],
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? $row : null;
    }

    /**
     * Finds an active Jazz event by its URL slug for the artist detail page.
     *
     * @return JazzArtistDetailEvent|null Null when no active Jazz event matches the slug.
     */
    public function findActiveJazzBySlug(string $slug): ?JazzArtistDetailEvent
    {
        $row = $this->queryActiveEventBySlug($slug, EventTypeId::Jazz);
        return $row !== null ? JazzArtistDetailEvent::fromRow($row) : null;
    }

    /**
     * @return JazzArtistCardRecord[]
     */
    public function findJazzOverviewArtists(): array
    {
        return $this->fetchJazzArtistCards(true);
    }

    /**
     * Finds an active Storytelling event by its URL slug for the detail page.
     *
     * @return StorytellingDetailEvent|null Null when no active Storytelling event matches the slug.
     */
    public function findActiveStorytellingBySlug(string $slug): ?StorytellingDetailEvent
    {
        $row = $this->queryActiveEventBySlug($slug, EventTypeId::Storytelling);
        return $row !== null ? StorytellingDetailEvent::fromRow($row) : null;
    }

    /**
     * @return JazzArtistCardRecord[]
     */
    private function fetchJazzArtistCards(bool $featuredOnly): array
    {
        $sql = '
            SELECT
                a.ArtistId,
                COALESCE(MIN(e.EventId), 0) AS EventId,
                COALESCE(
                    SUBSTRING_INDEX(
                        GROUP_CONCAT(
                            DISTINCT NULLIF(e.Slug, \'\')
                            ORDER BY e.EventId ASC
                            SEPARATOR \'||\'
                        ),
                        \'||\',
                        1
                    ),
                    \'\'
                ) AS Slug,
                COALESCE(NULLIF(a.Name, \'\'), \'\') AS ArtistName,
                COALESCE(NULLIF(a.Style, \'\'), \'Jazz\') AS ArtistStyle,
                COALESCE(NULLIF(a.CardDescription, \'\'), \'\') AS CardDescription,
                COALESCE(card_image.FilePath, \'\') AS ImageUrl,
                a.CardSortOrder,
                COUNT(DISTINCT es.EventSessionId) AS PerformanceCount,
                MIN(es.StartDateTime) AS FirstPerformanceAt,
                COALESCE(TRIM(SUBSTRING_INDEX(
                    GROUP_CONCAT(
                        TRIM(CONCAT_WS(\' \', COALESCE(sv.Name, v.Name, \'\'), COALESCE(NULLIF(es.HallName, \'\'), \'\')))
                        ORDER BY es.StartDateTime ASC
                        SEPARATOR \'||\'
                    ),
                    \'||\',
                    1
                )), \'\') AS FirstPerformanceLocation
            FROM Artist a
            LEFT JOIN MediaAsset card_image ON card_image.MediaAssetId = a.ImageAssetId
            LEFT JOIN Event e
                ON e.ArtistId = a.ArtistId
               AND e.EventTypeId = :eventTypeId
               AND e.IsActive = 1
            LEFT JOIN EventSession es
                ON es.EventId = e.EventId
               AND es.IsActive = 1
               AND es.IsCancelled = 0
            LEFT JOIN Venue sv ON sv.VenueId = es.VenueId
            LEFT JOIN Venue v ON v.VenueId = e.VenueId
            WHERE a.IsActive = 1
        ';

        $params = ['eventTypeId' => EventTypeId::Jazz->value];
        if ($featuredOnly) {
            $sql .= ' AND a.ShowOnJazzOverview = 1';
        }

        $sql .= '
            GROUP BY
                a.ArtistId,
                a.Name,
                a.Style,
                a.CardDescription,
                card_image.FilePath,
                a.CardSortOrder
            ORDER BY
                CASE WHEN a.CardSortOrder = 0 THEN 1 ELSE 0 END,
                a.CardSortOrder ASC,
                ArtistName ASC
        ';

        return $this->fetchAll(
            $sql,
            $params,
            fn(array $row) => JazzArtistCardRecord::fromRow($row),
        );
    }

    /**
     * @return Event|null Null if no event exists with the given ID.
     */
    public function findById(int $eventId): ?Event
    {
        return $this->fetchOne(
            'SELECT * FROM Event WHERE EventId = :eventId',
            ['eventId' => $eventId],
            fn(array $row) => Event::fromRow($row),
        );
    }

    /**
     * Inserts a new event. Nullable foreign keys (VenueId, ArtistId) allow events
     * that aren't tied to a specific venue or artist.
     *
     * @return int The auto-incremented EventId of the new row.
     */
    public function create(EventUpsertData $data): int
    {
        return $this->executeInsert(
            'INSERT INTO Event (
                EventTypeId, Title, Slug, ShortDescription, LongDescriptionHtml,
                FeaturedImageAssetId, VenueId, ArtistId, IsActive
            ) VALUES (
                :eventTypeId, :title, :slug, :shortDescription, :longDescriptionHtml,
                :featuredImageAssetId, :venueId, :artistId, :isActive
            )',
            [
                'eventTypeId' => $data->eventTypeId,
                'title' => $data->title,
                'slug' => $data->slug,
                'shortDescription' => $data->shortDescription,
                'longDescriptionHtml' => $data->longDescriptionHtml,
                'featuredImageAssetId' => $data->featuredImageAssetId,
                'venueId' => $data->venueId,
                'artistId' => $data->artistId,
                'isActive' => $data->isActive ? 1 : 0,
            ],
        );
    }

    /**
     * Updates all mutable fields of an event.
     */
    public function update(int $eventId, EventUpsertData $data): bool
    {
        $this->execute(
            'UPDATE Event SET
                Title = :title, ShortDescription = :shortDescription,
                LongDescriptionHtml = :longDescriptionHtml,
                FeaturedImageAssetId = :featuredImageAssetId,
                VenueId = :venueId, ArtistId = :artistId,
                IsActive = :isActive
            WHERE EventId = :eventId',
            [
                'eventId' => $eventId,
                'title' => $data->title,
                'shortDescription' => $data->shortDescription,
                'longDescriptionHtml' => $data->longDescriptionHtml,
                'featuredImageAssetId' => $data->featuredImageAssetId,
                'venueId' => $data->venueId,
                'artistId' => $data->artistId,
                'isActive' => $data->isActive ? 1 : 0,
            ],
        );

        return true;
    }

    /**
     * Checks whether any event row uses the given slug (case-sensitive).
     * Pass $excludeEventId to skip one row — used when updating an event's own slug.
     */
    public function slugExists(string $slug, ?int $excludeEventId = null): bool
    {
        if ($excludeEventId !== null) {
            $stmt = $this->execute(
                'SELECT EventId FROM Event WHERE Slug = :slug AND EventId != :excludeId LIMIT 1',
                ['slug' => $slug, 'excludeId' => $excludeEventId],
            );
        } else {
            $stmt = $this->execute(
                'SELECT EventId FROM Event WHERE Slug = :slug LIMIT 1',
                ['slug' => $slug],
            );
        }

        return $stmt->fetch() !== false;
    }

    /**
     * Hard-deletes an event. Will fail if foreign key constraints exist on related rows.
     * Prefer softDelete() for user-facing deactivation.
     */
    public function delete(int $eventId): bool
    {
        $this->execute('DELETE FROM Event WHERE EventId = :eventId', ['eventId' => $eventId]);

        return true;
    }

    /**
     * Checks whether an event row exists (regardless of active status).
     */
    public function exists(int $eventId): bool
    {
        $stmt = $this->execute(
            'SELECT EventId FROM Event WHERE EventId = :eventId',
            ['eventId' => $eventId],
        );

        return $stmt->fetch() !== false;
    }

    /**
     * Soft-deletes an event by setting IsActive = 0. The row remains in the database
     * for historical reference. Typically paired with deactivateSessions().
     */
    public function softDelete(int $eventId): bool
    {
        $this->execute('UPDATE Event SET IsActive = 0 WHERE EventId = :eventId', ['eventId' => $eventId]);

        return true;
    }

    /**
     * Finds a single active restaurant event by its URL slug.
     *
     * @return RestaurantDetailEvent|null Null if no matching active restaurant event exists.
     */
    public function findActiveRestaurantBySlug(string $slug): ?RestaurantDetailEvent
    {
        $row = $this->queryActiveEventBySlug($slug, EventTypeId::Restaurant);

        return $row !== null ? RestaurantDetailEvent::fromRow($row) : null;
    }

    /**
     * Returns all active restaurant-type events, ordered by EventId.
     *
     * @return RestaurantDetailEvent[]
     */
    public function findActiveRestaurantEvents(): array
    {
        return $this->fetchAll(
            'SELECT *
            FROM Event
            WHERE EventTypeId = :eventTypeId
              AND IsActive = 1
            ORDER BY EventId ASC',
            ['eventTypeId' => EventTypeId::Restaurant->value],
            fn(array $row) => RestaurantDetailEvent::fromRow($row),
        );
    }
}
