<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\EventTypeId;
use App\Infrastructure\Database;
use App\Models\Event;
use App\Models\EventFilter;
use App\Models\EventWithDetails;
use App\Models\JazzArtistDetailEvent;
use App\Models\StorytellingDetailEvent;
use App\Repositories\Interfaces\IEventRepository;
use PDO;

class EventRepository implements IEventRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

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

        if ($includeSessionCount) {
            $select .= ', COALESCE(es_count.SessionCount, 0) AS SessionCount, COALESCE(es_count.TotalSoldTickets, 0) AS TotalSoldTickets, COALESCE(es_count.TotalCapacity, 0) AS TotalCapacity';
        }

        $sql = $select . '
            FROM Event e
            LEFT JOIN Venue v ON e.VenueId = v.VenueId
            INNER JOIN EventType et ON e.EventTypeId = et.EventTypeId
        ';

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

        if ($dayOfWeek !== null && $dayOfWeek !== '') {
            $dayNumber = $this->dayNameToNumber($dayOfWeek);
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

    public function findActiveJazzBySlug(string $slug): ?JazzArtistDetailEvent
    {
        $row = $this->queryActiveEventBySlug($slug, EventTypeId::Jazz);
        return $row !== null ? JazzArtistDetailEvent::fromRow($row) : null;
    }

    public function findActiveStorytellingBySlug(string $slug): ?StorytellingDetailEvent
    {
        $row = $this->queryActiveEventBySlug($slug, EventTypeId::Storytelling);
        return $row !== null ? StorytellingDetailEvent::fromRow($row) : null;
    }

    private function dayNameToNumber(string $dayName): ?int
    {
        $map = [
            'sunday' => 1, 'monday' => 2, 'tuesday' => 3, 'wednesday' => 4,
            'thursday' => 5, 'friday' => 6, 'saturday' => 7,
        ];
        return $map[strtolower($dayName)] ?? null;
    }

    public function findById(int $eventId): ?Event
    {
        $stmt = $this->pdo->prepare('SELECT * FROM Event WHERE EventId = :eventId');
        $stmt->execute(['eventId' => $eventId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? Event::fromRow($result) : null;
    }

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

    public function delete(int $eventId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM Event WHERE EventId = :eventId');
        return $stmt->execute(['eventId' => $eventId]);
    }

    public function exists(int $eventId): bool
    {
        $stmt = $this->pdo->prepare('SELECT EventId FROM Event WHERE EventId = :eventId');
        $stmt->execute(['eventId' => $eventId]);
        return $stmt->fetch() !== false;
    }

    public function softDelete(int $eventId): bool
    {
        $stmt = $this->pdo->prepare('UPDATE Event SET IsActive = 0 WHERE EventId = :eventId');
        return $stmt->execute(['eventId' => $eventId]);
    }

    public function deactivateSessions(int $eventId): bool
    {
        $stmt = $this->pdo->prepare('UPDATE EventSession SET IsActive = 0 WHERE EventId = :eventId');
        return $stmt->execute(['eventId' => $eventId]);
    }
}
