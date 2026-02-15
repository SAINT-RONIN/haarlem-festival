<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\Event;
use App\Repositories\Interfaces\IEventRepository;
use PDO;

/**
 * Repository for Event database operations.
 */
class EventRepository implements IEventRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * @return array<int, array{EventId: int, EventTypeId: int, Title: string, ShortDescription: string, VenueName: ?string, EventTypeName: string}>
     */
    public function findAllByType(int $eventTypeId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT e.*, v.Name AS VenueName, et.Name AS EventTypeName
            FROM Event e
            LEFT JOIN Venue v ON e.VenueId = v.VenueId
            INNER JOIN EventType et ON e.EventTypeId = et.EventTypeId
            WHERE e.EventTypeId = :eventTypeId
            ORDER BY e.Title ASC
        ');
        $stmt->execute(['eventTypeId' => $eventTypeId]);
        return $stmt->fetchAll();
    }

    /**
     * @return array<int, array{EventId: int, EventTypeId: int, Title: string, VenueName: ?string, EventTypeName: string, EventTypeSlug: string, SessionCount: int, IsActive: bool}>
     */
    public function findAllWithDetails(): array
    {
        $stmt = $this->pdo->prepare('
            SELECT e.*, v.Name AS VenueName, et.Name AS EventTypeName, et.Slug AS EventTypeSlug,
                   (SELECT COUNT(*) FROM EventSession es WHERE es.EventId = e.EventId) AS SessionCount
            FROM Event e
            LEFT JOIN Venue v ON e.VenueId = v.VenueId
            INNER JOIN EventType et ON e.EventTypeId = et.EventTypeId
            WHERE e.IsActive = 1
            ORDER BY et.Name ASC, e.Title ASC
        ');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Finds all events with optional filtering by type and day.
     *
     * @return array<int, array{EventId: int, EventTypeId: int, Title: string, VenueName: ?string, EventTypeName: string, EventTypeSlug: string, SessionCount: int}>
     */
    public function findAllWithDetailsFiltered(?int $eventTypeId = null, ?string $dayOfWeek = null): array
    {
        $conditions = ['e.IsActive = 1'];
        $params = [];

        if ($eventTypeId !== null) {
            $conditions[] = 'e.EventTypeId = :eventTypeId';
            $params['eventTypeId'] = $eventTypeId;
        }

        // Day of week filtering requires a join with EventSession
        $dayJoin = '';
        if ($dayOfWeek !== null) {
            $dayJoin = 'INNER JOIN EventSession es_day ON es_day.EventId = e.EventId AND DAYNAME(es_day.StartDateTime) = :dayOfWeek';
            $params['dayOfWeek'] = $dayOfWeek;
        }

        $whereClause = implode(' AND ', $conditions);

        $stmt = $this->pdo->prepare("
            SELECT DISTINCT e.*, v.Name AS VenueName, et.Name AS EventTypeName, et.Slug AS EventTypeSlug,
                   (SELECT COUNT(*) FROM EventSession es WHERE es.EventId = e.EventId) AS SessionCount
            FROM Event e
            LEFT JOIN Venue v ON e.VenueId = v.VenueId
            INNER JOIN EventType et ON e.EventTypeId = et.EventTypeId
            {$dayJoin}
            WHERE {$whereClause}
            ORDER BY et.Name ASC, e.Title ASC
        ");
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $eventId): ?Event
    {
        $stmt = $this->pdo->prepare('SELECT * FROM Event WHERE EventId = :eventId');
        $stmt->execute(['eventId' => $eventId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? Event::fromRow($result) : null;
    }

    public function findByIdWithDetails(int $eventId): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT e.*, v.Name AS VenueName, et.Name AS EventTypeName, et.Slug AS EventTypeSlug
            FROM Event e
            LEFT JOIN Venue v ON e.VenueId = v.VenueId
            INNER JOIN EventType et ON e.EventTypeId = et.EventTypeId
            WHERE e.EventId = :eventId
        ');
        $stmt->execute(['eventId' => $eventId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO Event (EventTypeId, Title, ShortDescription, LongDescriptionHtml,
                FeaturedImageAssetId, VenueId, ArtistId, RestaurantId, IsActive)
            VALUES (:eventTypeId, :title, :shortDescription, :longDescriptionHtml,
                :featuredImageAssetId, :venueId, :artistId, :restaurantId, 1)
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

    public function update(int $eventId, array $data): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE Event SET Title = :title, ShortDescription = :shortDescription,
                LongDescriptionHtml = :longDescriptionHtml, VenueId = :venueId, IsActive = :isActive
            WHERE EventId = :eventId
        ');
        return $stmt->execute([
            'eventId' => $eventId,
            'title' => $data['Title'],
            'shortDescription' => $data['ShortDescription'] ?? '',
            'longDescriptionHtml' => $data['LongDescriptionHtml'] ?? '<p></p>',
            'venueId' => $data['VenueId'] ?? null,
            'isActive' => $data['IsActive'] ?? 1,
        ]);
    }

    public function delete(int $eventId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM Event WHERE EventId = :eventId');
        return $stmt->execute(['eventId' => $eventId]);
    }

    /**
     * Checks if an event exists.
     */
    public function exists(int $eventId): bool
    {
        $stmt = $this->pdo->prepare('SELECT EventId FROM Event WHERE EventId = :eventId');
        $stmt->execute(['eventId' => $eventId]);
        return $stmt->fetch() !== false;
    }

    /**
     * Soft deletes an event by setting IsActive = 0.
     */
    public function softDelete(int $eventId): bool
    {
        $stmt = $this->pdo->prepare('UPDATE Event SET IsActive = 0 WHERE EventId = :eventId');
        return $stmt->execute(['eventId' => $eventId]);
    }

    /**
     * Deactivates all sessions for an event.
     */
    public function deactivateSessions(int $eventId): bool
    {
        $stmt = $this->pdo->prepare('UPDATE EventSession SET IsActive = 0 WHERE EventId = :eventId');
        return $stmt->execute(['eventId' => $eventId]);
    }
}

