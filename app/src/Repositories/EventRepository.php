<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\EventTypeId;
use App\Models\Event;
use App\DTOs\Cms\EventUpsertData;
use App\DTOs\Filters\EventFilter;
use App\DTOs\Events\EventWithDetails;
use App\DTOs\Events\JazzArtistDetailEvent;
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
            'SELECT *
            FROM Event e
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
     * Inserts a new event. Nullable foreign keys (VenueId, ArtistId, RestaurantId)
     * allow events that aren't tied to a specific venue, artist, or restaurant.
     *
     * @return int The auto-incremented EventId of the new row.
     */
    public function create(EventUpsertData $data): int
    {
        return $this->executeInsert(
            'INSERT INTO Event (
                EventTypeId, Title, ShortDescription, LongDescriptionHtml,
                FeaturedImageAssetId, VenueId, ArtistId, RestaurantId, IsActive
            ) VALUES (
                :eventTypeId, :title, :shortDescription, :longDescriptionHtml,
                :featuredImageAssetId, :venueId, :artistId, :restaurantId, :isActive
            )',
            [
                'eventTypeId' => $data->eventTypeId,
                'title' => $data->title,
                'shortDescription' => $data->shortDescription,
                'longDescriptionHtml' => $data->longDescriptionHtml,
                'featuredImageAssetId' => $data->featuredImageAssetId,
                'venueId' => $data->venueId,
                'artistId' => $data->artistId,
                'restaurantId' => $data->restaurantId,
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
                RestaurantId = :restaurantId, IsActive = :isActive
            WHERE EventId = :eventId',
            [
                'eventId' => $eventId,
                'title' => $data->title,
                'shortDescription' => $data->shortDescription,
                'longDescriptionHtml' => $data->longDescriptionHtml,
                'featuredImageAssetId' => $data->featuredImageAssetId,
                'venueId' => $data->venueId,
                'artistId' => $data->artistId,
                'restaurantId' => $data->restaurantId,
                'isActive' => $data->isActive ? 1 : 0,
            ],
        );

        return true;
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
}
