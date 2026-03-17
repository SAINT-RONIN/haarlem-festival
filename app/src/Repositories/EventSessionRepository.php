<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Repositories\Interfaces\IEventSessionRepository;
use PDO;

class EventSessionRepository implements IEventSessionRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function findSessions(array $filters = []): array
    {
        $conditions = [];
        $params = [];

        if (isset($filters['eventId'])) {
            $conditions[] = 'es.EventId = :eventId';
            $params['eventId'] = (int)$filters['eventId'];
        }

        if (isset($filters['eventTypeId'])) {
            $conditions[] = 'e.EventTypeId = :eventTypeId';
            $params['eventTypeId'] = (int)$filters['eventTypeId'];
        }

        if (isset($filters['sessionId'])) {
            $conditions[] = 'es.EventSessionId = :sessionId';
            $params['sessionId'] = (int)$filters['sessionId'];
        }

        if (isset($filters['sessionIds']) && is_array($filters['sessionIds']) && $filters['sessionIds'] !== []) {
            $sessionIdPlaceholders = [];
            foreach ($filters['sessionIds'] as $index => $sid) {
                $key = 'sessionId_' . $index;
                $sessionIdPlaceholders[] = ':' . $key;
                $params[$key] = (int)$sid;
            }
            $conditions[] = 'es.EventSessionId IN (' . implode(',', $sessionIdPlaceholders) . ')';
        }

        if (array_key_exists('isActive', $filters)) {
            $conditions[] = 'es.IsActive = :isActive';
            $params['isActive'] = ((bool)$filters['isActive']) ? 1 : 0;
        }

        $includeCancelled = (bool)($filters['includeCancelled'] ?? false);
        if (!$includeCancelled) {
            $conditions[] = 'es.IsCancelled = 0';
        }

        if (isset($filters['eventIsActive'])) {
            $conditions[] = 'e.IsActive = :eventIsActive';
            $params['eventIsActive'] = ((bool)$filters['eventIsActive']) ? 1 : 0;
        }

        if (isset($filters['startDate']) && is_string($filters['startDate']) && $filters['startDate'] !== '') {
            $conditions[] = 'DATE(es.StartDateTime) >= :startDate';
            $params['startDate'] = $filters['startDate'];
        }

        if (isset($filters['endDate']) && is_string($filters['endDate']) && $filters['endDate'] !== '') {
            $conditions[] = 'DATE(es.StartDateTime) <= :endDate';
            $params['endDate'] = $filters['endDate'];
        }

        if (isset($filters['dayOfWeek']) && is_string($filters['dayOfWeek']) && $filters['dayOfWeek'] !== '') {
            $conditions[] = 'DAYNAME(es.StartDateTime) = :dayOfWeek';
            $params['dayOfWeek'] = $filters['dayOfWeek'];
        }

        $visibleDays = $filters['visibleDays'] ?? null;
        if (is_array($visibleDays)) {
            if ($visibleDays === []) {
                // Explicitly no visible days configured: force an empty result set.
                $conditions[] = '1 = 0';
            } elseif (count($visibleDays) < 7) {
                $dayLiterals = array_map(
                    static fn (mixed $day): int => (int)$day,
                    array_values($visibleDays)
                );
                $conditions[] = 'DAYOFWEEK(es.StartDateTime) - 1 IN (' . implode(',', $dayLiterals) . ')';
            }
        }

        $whereClause = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);
        $requestedOrderBy = is_string($filters['orderBy'] ?? null) ? $filters['orderBy'] : '';
        $allowedOrderBy = [
            'es.StartDateTime ASC',
            'es.StartDateTime DESC',
            'DATE(es.StartDateTime) ASC, es.StartDateTime ASC',
        ];
        $orderBy = in_array($requestedOrderBy, $allowedOrderBy, true)
            ? $requestedOrderBy
            : 'es.StartDateTime ASC';

        $baseFrom = '
            FROM EventSession es
            INNER JOIN Event e ON es.EventId = e.EventId
            INNER JOIN EventType et ON e.EventTypeId = et.EventTypeId
            LEFT JOIN Venue v ON e.VenueId = v.VenueId
            LEFT JOIN Artist a ON e.ArtistId = a.ArtistId
            LEFT JOIN MediaAsset ma ON a.ImageAssetId = ma.MediaAssetId
        ';

        $groupByDay = (bool)($filters['groupByDay'] ?? false);
        if ($groupByDay) {
            $maxDays = (int)($filters['maxDays'] ?? 7);
            if ($maxDays <= 0) {
                $maxDays = 7;
            }

            $daysSql = '
                SELECT DISTINCT DATE(es.StartDateTime) AS Date
                ' . $baseFrom . '
                ' . $whereClause . '
                ORDER BY Date ASC
                LIMIT :maxDays
            ';
            $daysStmt = $this->pdo->prepare($daysSql);
            foreach ($params as $key => $value) {
                $daysStmt->bindValue(':' . $key, $value);
            }
            $daysStmt->bindValue(':maxDays', $maxDays, PDO::PARAM_INT);
            $daysStmt->execute();
            $days = $daysStmt->fetchAll(PDO::FETCH_ASSOC);

            if ($days === []) {
                return ['days' => [], 'sessions' => []];
            }

            $dates = array_column($days, 'Date');
            $dateBindings = [];
            $datePlaceholders = [];
            foreach ($dates as $index => $date) {
                $placeholder = 'sessionDate' . $index;
                $datePlaceholders[] = ':' . $placeholder;
                $dateBindings[$placeholder] = $date;
            }
            $sessionDateCondition = 'DATE(es.StartDateTime) IN (' . implode(',', $datePlaceholders) . ')';
            $sessionsWhereClause = $whereClause === ''
                ? 'WHERE ' . $sessionDateCondition
                : $whereClause . ' AND ' . $sessionDateCondition;
            $sessionsSql = '
                SELECT
                    es.*,
                    DATE(es.StartDateTime) AS SessionDate,
                    DAYNAME(es.StartDateTime) AS DayOfWeek,
                    e.Title AS EventTitle,
                    e.EventTypeId,
                    et.Name AS EventTypeName,
                    et.Slug AS EventTypeSlug,
                    v.Name AS VenueName,
                    a.Name AS ArtistName,
                    ma.FilePath AS ArtistImageUrl
                ' . $baseFrom . '
                ' . $sessionsWhereClause . '
                ORDER BY ' . $orderBy;

            $prepared = $this->pdo->prepare($sessionsSql);
            $prepared->execute(array_merge($params, $dateBindings));
            $sessions = $prepared->fetchAll(PDO::FETCH_ASSOC);

            return ['days' => $days, 'sessions' => $sessions];
        }

        $sessionsSql = '
            SELECT
                es.*,
                DATE(es.StartDateTime) AS SessionDate,
                DAYNAME(es.StartDateTime) AS DayOfWeek,
                e.Title AS EventTitle,
                e.EventTypeId,
                et.Name AS EventTypeName,
                et.Slug AS EventTypeSlug,
                v.Name AS VenueName,
                a.Name AS ArtistName,
                ma.FilePath AS ArtistImageUrl
            ' . $baseFrom . '
            ' . $whereClause . '
            ORDER BY ' . $orderBy;

        $stmt = $this->pdo->prepare($sessionsSql);
        $stmt->execute($params);
        return ['sessions' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO EventSession (
                EventId, StartDateTime, EndDateTime, CapacityTotal,
                CapacitySingleTicketLimit, HallName, SessionType,
                DurationMinutes, LanguageCode, MinAge, MaxAge,
                ReservationRequired, IsFree, Notes, HistoryTicketLabel, CtaLabel, CtaUrl,
                IsCancelled, IsActive
            ) VALUES (
                :eventId, :startDateTime, :endDateTime, :capacityTotal,
                :capacitySingleTicketLimit, :hallName, :sessionType,
                :durationMinutes, :languageCode, :minAge, :maxAge,
                :reservationRequired, :isFree, :notes, :historyTicketLabel, :ctaLabel, :ctaUrl,
                0, 1
            )
        ');

        $stmt->execute([
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
            'historyTicketLabel' => $data['HistoryTicketLabel'] ?? null,
            'ctaLabel' => $data['CtaLabel'] ?? null,
            'ctaUrl' => $data['CtaUrl'] ?? null,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $sessionId, array $data): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE EventSession SET
                StartDateTime = :startDateTime,
                EndDateTime = :endDateTime,
                CapacityTotal = :capacityTotal,
                HallName = :hallName,
                LanguageCode = :languageCode,
                MinAge = :minAge,
                Notes = :notes,
                HistoryTicketLabel = :historyTicketLabel,
                CtaLabel = :ctaLabel,
                CtaUrl = :ctaUrl
            WHERE EventSessionId = :sessionId
        ');

        return $stmt->execute([
            'sessionId' => $sessionId,
            'startDateTime' => $data['StartDateTime'],
            'endDateTime' => $data['EndDateTime'],
            'capacityTotal' => $data['CapacityTotal'] ?? 100,
            'hallName' => $data['HallName'] ?? null,
            'languageCode' => $data['LanguageCode'] ?? null,
            'minAge' => $data['MinAge'] ?? null,
            'notes' => $data['Notes'] ?? '',
            'historyTicketLabel' => $data['HistoryTicketLabel'] ?? null,
            'ctaLabel' => $data['CtaLabel'] ?? null,
            'ctaUrl' => $data['CtaUrl'] ?? null,
        ]);
    }

    public function delete(int $sessionId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM EventSession WHERE EventSessionId = :sessionId');
        return $stmt->execute(['sessionId' => $sessionId]);
    }
}
