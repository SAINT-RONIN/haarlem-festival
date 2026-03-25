<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\EventTypeId;
use App\Models\Event;
use App\DTOs\Filters\EventFilter;
use App\DTOs\Events\EventWithDetails;
use App\DTOs\Events\JazzArtistDetailEvent;
use App\DTOs\Events\StorytellingDetailEvent;
use App\Helpers\FormatHelper;
use App\Repositories\Interfaces\IEventRepository;
use PDO;

/**
 * Manages CRUD operations on the Event table, with support for filtered listing
 * that joins Venue, EventType, and aggregated EventSession data.
 * Also provides slug-based lookups for public-facing Jazz and Storytelling detail pages.
 */
class EventRepository implements IEventRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * Retrieves events with optional filtering by active status, event type, specific event,
     * and day of week. Joins Venue and EventType for display names. When includeSessionCount
     * is set, attaches aggregate session/ticket counts via a subquery on EventSession.
     *
     * @return EventWithDetails[] Sorted by event type name, then event title. Empty array if no matches.
     */
    public function findEvents(EventFilter $filters = new EventFilter()): array
    {
        $includeSessionCount = (bool)($filters->includeSessionCount ?? false);
        $isActive = $filters->isActive;
        $eventTypeId = $filters->eventTypeId;
        $dayOfWeek = $filters->dayOfWeek;
        $eventId = $filters->eventId;

        $select = '
            SELECT DISTINCT
                e.*,
                v.Name AS VenueName,
                et.Name AS EventTypeName,
                et.Slug AS EventTypeSlug
        ';

        // Append aggregate columns: number of sessions, total sold tickets, and total capacity per event
        if ($includeSessionCount) {
            $select .= ', COALESCE(es_count.SessionCount, 0) AS SessionCount, COALESCE(es_count.TotalSoldTickets, 0) AS TotalSoldTickets, COALESCE(es_count.TotalCapacity, 0) AS TotalCapacity';
        }

        $sql = $select . '
            FROM Event e
            LEFT JOIN Venue v ON e.VenueId = v.VenueId
            INNER JOIN EventType et ON e.EventTypeId = et.EventTypeId
        ';

        // Left-join a derived table that aggregates session counts and ticket totals per event
        if ($includeSessionCount) {
            $sql .= '
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

        $conditions = [];
        $params = [];

        // Filter events to only those with at least one session on the requested day of week
        if ($dayOfWeek !== null && $dayOfWeek !== '') {
            $dayNumber = FormatHelper::dayNameToMysqlDayOfWeek($dayOfWeek);
            if ($dayNumber !== null) {
                $sql .= '
                    INNER JOIN EventSession es_day
                        ON es_day.EventId = e.EventId
                        AND DAYOFWEEK(es_day.StartDateTime) = :dayOfWeekNum
                ';
                $params['dayOfWeekNum'] = $dayNumber;
            }
        }

        if ($isActive !== null) {
            $conditions[] = 'e.IsActive = :isActive';
            $params['isActive'] = $isActive ? 1 : 0;
        }

        if ($eventTypeId !== null) {
            $conditions[] = 'e.EventTypeId = :eventTypeId';
            $params['eventTypeId'] = (int)$eventTypeId;
        }

        if ($eventId !== null) {
            $conditions[] = 'e.EventId = :eventId';
            $params['eventId'] = (int)$eventId;
        }

        if ($conditions !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY et.Name ASC, e.Title ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return array_map([EventWithDetails::class, 'fromRow'], $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * Looks up a single active event by its URL slug and event type.
     *
     * @return array<string, mixed>|null Raw row data, or null if not found.
     */
    private function queryActiveEventBySlug(string $slug, EventTypeId $eventType): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM Event e
            WHERE e.EventTypeId = :eventTypeId
              AND e.IsActive = 1
              AND e.Slug = :slug
            LIMIT 1
        ');

        $stmt->execute([
            'eventTypeId' => $eventType->value,
            'slug' => $slug,
        ]);

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
        $stmt = $this->pdo->prepare('SELECT * FROM Event WHERE EventId = :eventId');
        $stmt->execute(['eventId' => $eventId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? Event::fromRow($result) : null;
    }

    /**
     * Inserts a new event with IsActive defaulting to 1 (active).
     * Nullable foreign keys (VenueId, ArtistId, RestaurantId) allow events
     * that aren't tied to a specific venue, artist, or restaurant.
     *
     * @param array<string, mixed> $data Event fields keyed by column name.
     * @return int The auto-incremented EventId of the new row.
     */
    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO Event (
                EventTypeId,
                Title,
                ShortDescription,
                LongDescriptionHtml,
                FeaturedImageAssetId,
                VenueId,
                ArtistId,
                RestaurantId,
                IsActive
            )
            VALUES (
                :eventTypeId,
                :title,
                :shortDescription,
                :longDescriptionHtml,
                :featuredImageAssetId,
                :venueId,
                :artistId,
                :restaurantId,
                1
            )
        ');

        $stmt->execute([
            'eventTypeId' => $data['EventTypeId'],
            'title' => $data['Title'],
            'shortDescription' => $data['ShortDescription'] ?? '',
            'longDescriptionHtml' => $data['LongDescriptionHtml'] ?? '<p></p>',
            'featuredImageAssetId' => $data['FeaturedImageAssetId'] ?? null,
            'venueId' => $data['VenueId'] ?? null,
            'artistId' => $data['ArtistId'] ?? null,
            'restaurantId' => $data['RestaurantId'] ?? null,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Updates all mutable fields of an event. Numeric foreign keys are coerced to int
     * or set to null when missing/non-numeric, preventing invalid FK references.
     *
     * @param array<string, mixed> $data Event fields keyed by column name.
     */
    public function update(int $eventId, array $data): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE Event
            SET
                Title = :title,
                ShortDescription = :shortDescription,
                LongDescriptionHtml = :longDescriptionHtml,
                FeaturedImageAssetId = :featuredImageAssetId,
                VenueId = :venueId,
                ArtistId = :artistId,
                RestaurantId = :restaurantId,
                IsActive = :isActive
            WHERE EventId = :eventId
        ');

        return $stmt->execute([
            'eventId' => $eventId,
            'title' => $data['Title'],
            'shortDescription' => $data['ShortDescription'] ?? '',
            'longDescriptionHtml' => $data['LongDescriptionHtml'] ?? '<p></p>',
            'featuredImageAssetId' => isset($data['FeaturedImageAssetId']) && is_numeric($data['FeaturedImageAssetId']) ? (int)$data['FeaturedImageAssetId'] : null,
            'venueId' => $data['VenueId'] ?? null,
            'artistId' => isset($data['ArtistId']) && is_numeric($data['ArtistId']) ? (int)$data['ArtistId'] : null,
            'restaurantId' => isset($data['RestaurantId']) && is_numeric($data['RestaurantId']) ? (int)$data['RestaurantId'] : null,
            'isActive' => $data['IsActive'] ?? 1,
        ]);
    }

    /**
     * Hard-deletes an event. Will fail if foreign key constraints exist on related rows.
     * Prefer softDelete() for user-facing deactivation.
     */
    public function delete(int $eventId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM Event WHERE EventId = :eventId');
        return $stmt->execute(['eventId' => $eventId]);
    }

    /**
     * Checks whether an event row exists (regardless of active status).
     */
    public function exists(int $eventId): bool
    {
        $stmt = $this->pdo->prepare('SELECT EventId FROM Event WHERE EventId = :eventId');
        $stmt->execute(['eventId' => $eventId]);
        return $stmt->fetch() !== false;
    }

    /**
     * Soft-deletes an event by setting IsActive = 0. The row remains in the database
     * for historical reference. Typically paired with deactivateSessions().
     */
    public function softDelete(int $eventId): bool
    {
        $stmt = $this->pdo->prepare('UPDATE Event SET IsActive = 0 WHERE EventId = :eventId');
        return $stmt->execute(['eventId' => $eventId]);
    }

}
