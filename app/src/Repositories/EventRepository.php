<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\Event;
use App\Repositories\Interfaces\IEventRepository;
use PDO;

class EventRepository implements IEventRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function findEvents(array $filters = []): array
    {
        $includeSessionCount = (bool)($filters['includeSessionCount'] ?? false);
        $isActive = array_key_exists('isActive', $filters) ? (bool)$filters['isActive'] : null;
        $eventTypeId = $filters['eventTypeId'] ?? null;
        $dayOfWeek = $filters['dayOfWeek'] ?? null;
        $eventId = $filters['eventId'] ?? null;

        $select = '
            SELECT DISTINCT
                e.*,
                v.Name AS VenueName,
                et.Name AS EventTypeName,
                et.Slug AS EventTypeSlug
        ';

        if ($includeSessionCount) {
            $select .= ',
                (SELECT COUNT(*) FROM EventSession es_count WHERE es_count.EventId = e.EventId) AS SessionCount
            ';
        }

        $sql = $select . '
            FROM Event e
            LEFT JOIN Venue v ON e.VenueId = v.VenueId
            INNER JOIN EventType et ON e.EventTypeId = et.EventTypeId
        ';

        $conditions = [];
        $params = [];

        if ($dayOfWeek !== null && $dayOfWeek !== '') {
            $sql .= '
                INNER JOIN EventSession es_day
                    ON es_day.EventId = e.EventId
                    AND DAYNAME(es_day.StartDateTime) = :dayOfWeek
            ';
            $params['dayOfWeek'] = $dayOfWeek;
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

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                VenueId = :venueId,
                IsActive = :isActive
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
