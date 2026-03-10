<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Repositories\Interfaces\IEventSessionRepository;
use PDO;

/**
 * Repository for EventSession database operations.
 */
class EventSessionRepository implements IEventSessionRepository
{
    private PDO $pdo;
    /** @var array<string>|null */
    private ?array $eventSessionColumns = null;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Returns upcoming sessions with event and type details.
     *
     * Joins EventSession with Event and EventType to get all needed data
     * for the homepage schedule display.
     *
     * @return array Array of session data with event title and type slug
     */
    public function findUpcomingWithDetails(): array
    {
        $stmt = $this->pdo->prepare('
            SELECT 
                es.EventSessionId,
                es.StartDateTime,
                es.EndDateTime,
                e.Title AS EventTitle,
                et.Name AS EventTypeName,
                et.Slug AS EventTypeSlug
            FROM EventSession es
            INNER JOIN Event e ON es.EventId = e.EventId
            INNER JOIN EventType et ON e.EventTypeId = et.EventTypeId
            WHERE es.IsActive = 1 
              AND es.IsCancelled = 0
              AND e.IsActive = 1
            ORDER BY es.StartDateTime ASC
        ');
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Returns schedule data for any event type.
     * Auto-discovers days from sessions, optionally filtered by date range.
     *
     * @param int $eventTypeId Event type to filter by
     * @param int $maxDays Maximum number of days to return (default 7)
     * @param string|null $startDate Optional start date filter (Y-m-d format)
     * @param string|null $endDate Optional end date filter (Y-m-d format)
     * @param array|null $visibleDays Optional array of visible day numbers (0=Sun, 1=Mon, etc.)
     * @return array Array with 'days' and 'sessions' keys
     */
    public function findScheduleDataByEventType(
        int     $eventTypeId,
        int     $maxDays = 7,
        ?string $startDate = null,
        ?string $endDate = null,
        ?array  $visibleDays = null
    ): array
    {
        $hasCtaLabel = $this->hasEventSessionColumn('CtaLabel');
        $hasCtaUrl = $this->hasEventSessionColumn('CtaUrl');
        $hasHistoryTicketLabel = $this->hasEventSessionColumn('HistoryTicketLabel');

        $ctaLabelSelect = $hasCtaLabel ? 'es.CtaLabel AS CtaLabel' : 'NULL AS CtaLabel';
        $ctaUrlSelect = $hasCtaUrl ? 'es.CtaUrl AS CtaUrl' : 'NULL AS CtaUrl';
        $historyTicketLabelSelect = $hasHistoryTicketLabel
            ? 'es.HistoryTicketLabel AS HistoryTicketLabel'
            : 'NULL AS HistoryTicketLabel';

        // Build date filter clause
        $dateFilter = '';
        $dateParams = [];

        if ($startDate !== null) {
            $dateFilter .= ' AND DATE(es.StartDateTime) >= :startDate';
            $dateParams['startDate'] = $startDate;
        }
        if ($endDate !== null) {
            $dateFilter .= ' AND DATE(es.StartDateTime) <= :endDate';
            $dateParams['endDate'] = $endDate;
        }

        // Filter by visible days of week
        $dayOfWeekFilter = '';
        if ($visibleDays !== null && count($visibleDays) < 7) {
            $dayPlaceholders = implode(',', array_map(fn($d) => (int)$d, $visibleDays));
            $dayOfWeekFilter = " AND DAYOFWEEK(es.StartDateTime) - 1 IN ({$dayPlaceholders})";
        }

        // Step 1: Get distinct dates that have sessions for this event type (auto-discover days)
        $daysStmt = $this->pdo->prepare("
            SELECT DISTINCT DATE(es.StartDateTime) AS Date
            FROM EventSession es
            INNER JOIN Event e ON es.EventId = e.EventId
            WHERE e.EventTypeId = :eventTypeId
              AND es.IsActive = 1
              AND es.IsCancelled = 0
              AND e.IsActive = 1
              {$dateFilter}
              {$dayOfWeekFilter}
            ORDER BY Date ASC
            LIMIT :maxDays
        ");
        $daysStmt->bindValue(':eventTypeId', $eventTypeId, \PDO::PARAM_INT);
        $daysStmt->bindValue(':maxDays', $maxDays, \PDO::PARAM_INT);
        foreach ($dateParams as $key => $value) {
            $daysStmt->bindValue(':' . $key, $value);
        }
        $daysStmt->execute();
        $days = $daysStmt->fetchAll();

        if (empty($days)) {
            return ['days' => [], 'sessions' => []];
        }

        // Extract dates for the query
        $dates = array_column($days, 'Date');
        $datePlaceholders = implode(',', array_fill(0, count($dates), '?'));

        // Step 2: Get sessions for those dates with all required fields
        $sessionsStmt = $this->pdo->prepare("
            SELECT 
                es.EventSessionId,
                es.EventId,
                es.StartDateTime,
                es.EndDateTime,
                {$ctaLabelSelect},
                {$ctaUrlSelect},
                es.CapacityTotal,
                es.SoldSingleTickets,
                es.SoldReservedSeats,
                es.HallName,
                {$historyTicketLabelSelect},
                DATE(es.StartDateTime) AS SessionDate,
                e.Title AS EventTitle,
                e.EventTypeId,
                et.Slug AS EventTypeSlug,
                v.Name AS VenueName,
                a.Name AS ArtistName,
                ma.FilePath AS ArtistImageUrl
            FROM EventSession es
            INNER JOIN Event e ON es.EventId = e.EventId
            INNER JOIN EventType et ON e.EventTypeId = et.EventTypeId
            LEFT JOIN Venue v ON e.VenueId = v.VenueId
            LEFT JOIN Artist a ON e.ArtistId = a.ArtistId
            LEFT JOIN MediaAsset ma ON a.ImageAssetId = ma.MediaAssetId
            WHERE e.EventTypeId = ?
              AND es.IsActive = 1
              AND es.IsCancelled = 0
              AND e.IsActive = 1
              AND DATE(es.StartDateTime) IN ($datePlaceholders)
            ORDER BY DATE(es.StartDateTime) ASC, es.StartDateTime ASC
        ");

        // Bind event type ID first, then dates
        $params = array_merge([$eventTypeId], $dates);
        $sessionsStmt->execute($params);
        $sessions = $sessionsStmt->fetchAll();

        return [
            'days' => $days,
            'sessions' => $sessions,
        ];
    }

    /**
     * Returns storytelling schedule data.
     *
     * Fetches up to 7 active days for storytelling (EventTypeId = 4).
     *
     * @param array|null $visibleDays Optional array of visible day numbers (0=Sun, 1=Mon, etc.)
     * @return array Array with 'days' and 'sessions' keys
     */
    public function findStorytellingScheduleData(?array $visibleDays = null): array
    {
        // Show all storytelling sessions, up to 7 days
        // Filter by visible days if specified
        return $this->findScheduleDataByEventType(
            eventTypeId: 4,
            maxDays: 7,
            visibleDays: $visibleDays
        );
    }

    /**
     * Find a session by ID with event and venue details.
     *
     * @param int $sessionId
     * @return array|null Session data or null if not found
     */
    public function findByIdWithDetails(int $sessionId): ?array
    {
        $stmt = $this->pdo->prepare('
            SELECT 
                es.*,
                e.Title AS EventTitle,
                e.EventTypeId,
                v.Name AS VenueName
            FROM EventSession es
            INNER JOIN Event e ON es.EventId = e.EventId
            LEFT JOIN Venue v ON e.VenueId = v.VenueId
            WHERE es.EventSessionId = :sessionId
        ');
        $stmt->execute(['sessionId' => $sessionId]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Find all sessions for an event.
     *
     * @param int $eventId
     * @return array Array of session rows
     */
    public function findByEventId(int $eventId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT *
            FROM EventSession
            WHERE EventId = :eventId
            ORDER BY StartDateTime ASC
        ');
        $stmt->execute(['eventId' => $eventId]);

        return $stmt->fetchAll();
    }

    /**
     * Create a new event session.
     *
     * @param array $data Session data
     * @return int The new session ID
     */
    public function create(array $data): int
    {
        $columns = [
            'EventId',
            'StartDateTime',
            'EndDateTime',
            'CapacityTotal',
            'CapacitySingleTicketLimit',
            'HallName',
            'SessionType',
            'DurationMinutes',
            'LanguageCode',
            'MinAge',
            'MaxAge',
            'ReservationRequired',
            'IsFree',
            'Notes',
        ];

        $placeholders = [
            ':eventId',
            ':startDateTime',
            ':endDateTime',
            ':capacityTotal',
            ':capacitySingleTicketLimit',
            ':hallName',
            ':sessionType',
            ':durationMinutes',
            ':languageCode',
            ':minAge',
            ':maxAge',
            ':reservationRequired',
            ':isFree',
            ':notes',
        ];

        $params = [
            'eventId' => $data['EventId'],
            'startDateTime' => $data['StartDateTime'],
            'endDateTime' => $data['EndDateTime'],
            'capacityTotal' => $data['CapacityTotal'] ?? 100,
            'capacitySingleTicketLimit' => $data['CapacitySingleTicketLimit'] ?? 100,
            'hallName' => $data['HallName'] ?? null,
            'sessionType' => $data['SessionType'] ?? null,
            'durationMinutes' => $data['DurationMinutes'] ?? null,
            'languageCode' => $data['LanguageCode'] ?? null,
            'minAge' => $data['MinAge'] ?? null,
            'maxAge' => $data['MaxAge'] ?? null,
            'reservationRequired' => $data['ReservationRequired'] ?? 0,
            'isFree' => $data['IsFree'] ?? 0,
            'notes' => $data['Notes'] ?? '',
        ];

        if ($this->hasEventSessionColumn('HistoryTicketLabel')) {
            $columns[] = 'HistoryTicketLabel';
            $placeholders[] = ':historyTicketLabel';
            $params['historyTicketLabel'] = $data['HistoryTicketLabel'] ?? null;
        }
        if ($this->hasEventSessionColumn('CtaLabel')) {
            $columns[] = 'CtaLabel';
            $placeholders[] = ':ctaLabel';
            $params['ctaLabel'] = $data['CtaLabel'] ?? null;
        }
        if ($this->hasEventSessionColumn('CtaUrl')) {
            $columns[] = 'CtaUrl';
            $placeholders[] = ':ctaUrl';
            $params['ctaUrl'] = $data['CtaUrl'] ?? null;
        }

        $columnsList = implode(', ', array_merge($columns, ['IsCancelled', 'IsActive']));
        $placeholdersList = implode(', ', array_merge($placeholders, ['0', '1']));
        $stmt = $this->pdo->prepare("INSERT INTO EventSession ({$columnsList}) VALUES ({$placeholdersList})");
        $stmt->execute($params);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Update an event session.
     *
     * @param int $sessionId
     * @param array $data Session data to update
     * @return bool Success status
     */
    public function update(int $sessionId, array $data): bool
    {
        $setParts = [
            'StartDateTime = :startDateTime',
            'EndDateTime = :endDateTime',
            'CapacityTotal = :capacityTotal',
            'HallName = :hallName',
            'LanguageCode = :languageCode',
            'MinAge = :minAge',
            'Notes = :notes',
        ];

        $params = [
            'sessionId' => $sessionId,
            'startDateTime' => $data['StartDateTime'],
            'endDateTime' => $data['EndDateTime'],
            'capacityTotal' => $data['CapacityTotal'] ?? 100,
            'hallName' => $data['HallName'] ?? null,
            'languageCode' => $data['LanguageCode'] ?? null,
            'minAge' => $data['MinAge'] ?? null,
            'notes' => $data['Notes'] ?? '',
        ];

        if ($this->hasEventSessionColumn('HistoryTicketLabel')) {
            $setParts[] = 'HistoryTicketLabel = :historyTicketLabel';
            $params['historyTicketLabel'] = $data['HistoryTicketLabel'] ?? null;
        }
        if ($this->hasEventSessionColumn('CtaLabel')) {
            $setParts[] = 'CtaLabel = :ctaLabel';
            $params['ctaLabel'] = $data['CtaLabel'] ?? null;
        }
        if ($this->hasEventSessionColumn('CtaUrl')) {
            $setParts[] = 'CtaUrl = :ctaUrl';
            $params['ctaUrl'] = $data['CtaUrl'] ?? null;
        }

        $setClause = implode(', ', $setParts);
        $stmt = $this->pdo->prepare("UPDATE EventSession SET {$setClause} WHERE EventSessionId = :sessionId");
        return $stmt->execute($params);
    }

    /**
     * Delete an event session.
     *
     * @param int $sessionId
     * @return bool Success status
     */
    public function delete(int $sessionId): bool
    {
        $stmt = $this->pdo->prepare('
            DELETE FROM EventSession
            WHERE EventSessionId = :sessionId
        ');

        return $stmt->execute(['sessionId' => $sessionId]);
    }

    private function hasEventSessionColumn(string $column): bool
    {
        if ($this->eventSessionColumns === null) {
            $stmt = $this->pdo->query('SHOW COLUMNS FROM EventSession');
            $columnNames = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            $this->eventSessionColumns = array_map(
                static fn($name): string => strtolower((string)$name),
                is_array($columnNames) ? $columnNames : []
            );
        }

        return in_array(strtolower($column), $this->eventSessionColumns, true);
    }
}
