<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\PriceTierId;
use App\Infrastructure\Database;
use App\Models\EventSessionFilter;
use App\Models\ScheduleDayData;
use App\Models\SessionQueryResult;
use App\Models\SessionWithEvent;
use App\Repositories\Interfaces\IEventSessionRepository;
use PDO;

/**
 * Queries and manages rows in the EventSession table. Supports heavily-filtered session
 * listing with joins to Event, EventType, Venue, Artist, and MediaAsset. Provides both
 * flat session lists and day-grouped results for the public schedule UI.
 */
class EventSessionRepository implements IEventSessionRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Builds and executes a dynamic query for event sessions with extensive filter support:
     * event type, date range, day of week, time of day, price type (free/fixed/pay-what-you-like),
     * venue, language, minimum age, and visible-days whitelist.
     *
     * When groupByDay is enabled, runs a two-pass query: first fetches distinct dates (capped
     * by maxDays), then retrieves sessions only within those dates. This powers the paginated
     * day-tab schedule UI.
     *
     * @return SessionQueryResult Contains sessions and optionally day metadata. Returns empty
     *                            collections when no sessions match.
     */
    public function findSessions(EventSessionFilter $filters = new EventSessionFilter()): SessionQueryResult
    {
        $conditions = [];
        $params = [];

        if ($filters->eventId !== null) {
            $conditions[] = 'es.EventId = :eventId';
            $params['eventId'] = $filters->eventId;
        }

        if ($filters->eventTypeId !== null) {
            $conditions[] = 'e.EventTypeId = :eventTypeId';
            $params['eventTypeId'] = $filters->eventTypeId;
        }

        if ($filters->sessionId !== null) {
            $conditions[] = 'es.EventSessionId = :sessionId';
            $params['sessionId'] = $filters->sessionId;
        }

        // Build dynamic IN clause with numbered placeholders to filter by multiple session IDs
        if ($filters->sessionIds !== null && $filters->sessionIds !== []) {
            $sessionIdPlaceholders = [];
            foreach ($filters->sessionIds as $index => $sid) {
                $key = 'sessionId_' . $index;
                $sessionIdPlaceholders[] = ':' . $key;
                $params[$key] = (int)$sid;
            }
            $conditions[] = 'es.EventSessionId IN (' . implode(',', $sessionIdPlaceholders) . ')';
        }

        if ($filters->isActive !== null) {
            $conditions[] = 'es.IsActive = :isActive';
            $params['isActive'] = $filters->isActive ? 1 : 0;
        }

        $includeCancelled = (bool)($filters->includeCancelled ?? false);
        if (!$includeCancelled) {
            $conditions[] = 'es.IsCancelled = 0';
        }

        if ($filters->eventIsActive !== null) {
            $conditions[] = 'e.IsActive = :eventIsActive';
            $params['eventIsActive'] = $filters->eventIsActive ? 1 : 0;
        }

        if ($filters->startDate !== null) {
            $conditions[] = 'DATE(es.StartDateTime) >= :startDate';
            $params['startDate'] = $filters->startDate;
        }

        if ($filters->endDate !== null) {
            $conditions[] = 'DATE(es.StartDateTime) <= :endDate';
            $params['endDate'] = $filters->endDate;
        }

        if ($filters->dayOfWeek !== null && $filters->dayOfWeek !== '') {
            $dayNumber = $this->dayNameToNumber($filters->dayOfWeek);
            if ($dayNumber !== null) {
                $conditions[] = 'DAYOFWEEK(es.StartDateTime) = :dayOfWeekNum';
                $params['dayOfWeekNum'] = $dayNumber;
            }
        }

        // visibleDays restricts results to specific weekdays (0=Sunday..6=Saturday).
        // Uses DAYOFWEEK()-1 to convert MySQL's 1-based numbering to 0-based.
        $visibleDays = $filters->visibleDays;
        if (is_array($visibleDays)) {
            if ($visibleDays === []) {
                // Explicitly no visible days configured: force an empty result set.
                $conditions[] = '1 = 0';
            } elseif (count($visibleDays) < 7) {
                $dayParams = [];
                foreach (array_values($visibleDays) as $index => $day) {
                    $key = 'visibleDay' . $index;
                    $dayParams[] = ':' . $key;
                    $params[$key] = (int)$day;
                }
                $conditions[] = 'DAYOFWEEK(es.StartDateTime) - 1 IN (' . implode(',', $dayParams) . ')';
            }
        }

        if ($filters->timeRange !== null) {
            match ($filters->timeRange) {
                'morning'   => $conditions[] = 'HOUR(es.StartDateTime) < 12',
                'afternoon' => $conditions[] = '(HOUR(es.StartDateTime) >= 12 AND HOUR(es.StartDateTime) < 17)',
                'evening'   => $conditions[] = 'HOUR(es.StartDateTime) >= 17',
            };
        }

        // Price type filter uses correlated subqueries against EventSessionPrice to classify
        // sessions as free, fixed-price, or pay-what-you-like
        if ($filters->priceType !== null) {
            $params['pwylTierId'] = PriceTierId::PayWhatYouLike->value;
            match ($filters->priceType) {
                'pay-what-you-like' => $conditions[] = 'EXISTS (SELECT 1 FROM EventSessionPrice esp WHERE esp.EventSessionId = es.EventSessionId AND esp.PriceTierId = :pwylTierId)',
                'free'              => $conditions[] = '(es.IsFree = 1 OR NOT EXISTS (SELECT 1 FROM EventSessionPrice esp WHERE esp.EventSessionId = es.EventSessionId AND esp.Price > 0))',
                'fixed'             => $conditions[] = 'EXISTS (SELECT 1 FROM EventSessionPrice esp WHERE esp.EventSessionId = es.EventSessionId AND esp.Price > 0 AND esp.PriceTierId != :pwylTierId)',
                default             => null,
            };
        }

        if ($filters->venueName !== null && $filters->venueName !== '') {
            $conditions[] = 'LOWER(v.Name) = :venueName';
            $params['venueName'] = strtolower($filters->venueName);
        }

        if ($filters->languageCode !== null && $filters->languageCode !== '') {
            $conditions[] = 'LOWER(es.LanguageCode) = :languageCode';
            $params['languageCode'] = strtolower($filters->languageCode);
        }

        if ($filters->filterMinAge !== null && $filters->filterMinAge > 0) {
            $conditions[] = '(es.MinAge IS NOT NULL AND es.MinAge >= :filterMinAge)';
            $params['filterMinAge'] = $filters->filterMinAge;
        }

        $whereClause = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);
        // Whitelist-based ORDER BY to prevent SQL injection via user-supplied sort values
        $requestedOrderBy = is_string($filters->orderBy) ? $filters->orderBy : '';
        $allowedOrderBy = [
            'es.StartDateTime ASC',
            'es.StartDateTime DESC',
            'DATE(es.StartDateTime) ASC, es.StartDateTime ASC',
        ];
        $orderBy = in_array($requestedOrderBy, $allowedOrderBy, true)
            ? $requestedOrderBy
            : 'es.StartDateTime ASC';

        // Base FROM clause joins session -> event -> event type, plus optional venue, artist,
        // and artist image. LEFT JOINs allow sessions whose events lack a venue or artist.
        $baseFrom = '
            FROM EventSession es
            INNER JOIN Event e ON es.EventId = e.EventId
            INNER JOIN EventType et ON e.EventTypeId = et.EventTypeId
            LEFT JOIN Venue v ON e.VenueId = v.VenueId
            LEFT JOIN Artist a ON e.ArtistId = a.ArtistId
            LEFT JOIN MediaAsset ma ON a.ImageAssetId = ma.MediaAssetId
        ';

        // Two-pass day-grouped mode: first query fetches distinct dates, second fetches sessions
        // only within those dates. This enables the schedule UI to show a limited number of day tabs.
        $groupByDay = (bool)($filters->groupByDay ?? false);
        if ($groupByDay) {
            $maxDays = (int)($filters->maxDays ?? 7);
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
                return new SessionQueryResult(sessions: [], days: []);
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
                    e.Slug AS EventSlug,
                    e.EventTypeId,
                    et.Name AS EventTypeName,
                    et.Slug AS EventTypeSlug,
                    v.Name AS VenueName,
                    a.Name AS ArtistName,
                    ma.FilePath AS ArtistImageUrl
                ' . $baseFrom . '
                ' . $sessionsWhereClause . '
                ORDER BY ' . $orderBy;

            if ($filters->limit !== null && $filters->limit > 0) {
                $sessionsSql .= ' LIMIT ' . (int) $filters->limit;
            }

            $prepared = $this->pdo->prepare($sessionsSql);
            $prepared->execute(array_merge($params, $dateBindings));
            $sessionRows = $prepared->fetchAll(PDO::FETCH_ASSOC);

            return new SessionQueryResult(
                sessions: array_map([SessionWithEvent::class, 'fromRow'], $sessionRows),
                days: array_map([ScheduleDayData::class, 'fromRow'], $days),
            );
        }

        $sessionsSql = '
            SELECT
                es.*,
                DATE(es.StartDateTime) AS SessionDate,
                DAYNAME(es.StartDateTime) AS DayOfWeek,
                e.Title AS EventTitle,
                e.Slug AS EventSlug,
                e.EventTypeId,
                et.Name AS EventTypeName,
                et.Slug AS EventTypeSlug,
                v.Name AS VenueName,
                a.Name AS ArtistName,
                ma.FilePath AS ArtistImageUrl
            ' . $baseFrom . '
            ' . $whereClause . '
            ORDER BY ' . $orderBy;

        if ($filters->limit !== null && $filters->limit > 0) {
            $sessionsSql .= ' LIMIT ' . (int) $filters->limit;
        }

        $stmt = $this->pdo->prepare($sessionsSql);
        $stmt->execute($params);
        return new SessionQueryResult(
            sessions: array_map([SessionWithEvent::class, 'fromRow'], $stmt->fetchAll(PDO::FETCH_ASSOC)),
        );
    }

    /**
     * Inserts a new session with IsCancelled=0 and IsActive=1 by default.
     *
     * @param array<string, mixed> $data Session fields keyed by column name.
     * @return int The auto-incremented EventSessionId.
     */
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

    /**
     * Updates all mutable session fields. Checkbox-style booleans (ReservationRequired, IsFree,
     * IsCancelled, IsActive) are derived from key presence via isset(), matching HTML form behavior
     * where unchecked checkboxes are absent from POST data.
     *
     * @param array<string, mixed> $data Session fields keyed by column name.
     */
    public function update(int $sessionId, array $data): bool
    {
        $stmt = $this->pdo->prepare('
            UPDATE EventSession SET
                StartDateTime = :startDateTime,
                EndDateTime = :endDateTime,
                CapacityTotal = :capacityTotal,
                CapacitySingleTicketLimit = :capacitySingleTicketLimit,
                HallName = :hallName,
                SessionType = :sessionType,
                DurationMinutes = :durationMinutes,
                LanguageCode = :languageCode,
                MinAge = :minAge,
                MaxAge = :maxAge,
                ReservationRequired = :reservationRequired,
                IsFree = :isFree,
                Notes = :notes,
                HistoryTicketLabel = :historyTicketLabel,
                CtaLabel = :ctaLabel,
                CtaUrl = :ctaUrl,
                IsCancelled = :isCancelled,
                IsActive = :isActive
            WHERE EventSessionId = :sessionId
        ');

        return $stmt->execute([
            'sessionId'                 => $sessionId,
            'startDateTime'             => $data['StartDateTime'],
            'endDateTime'               => $data['EndDateTime'],
            'capacityTotal'             => (int)($data['CapacityTotal'] ?? 100),
            'capacitySingleTicketLimit' => (int)($data['CapacitySingleTicketLimit'] ?? 100),
            'hallName'                  => $data['HallName'] ?? null,
            'sessionType'               => $data['SessionType'] ?? null,
            'durationMinutes'           => isset($data['DurationMinutes']) && $data['DurationMinutes'] !== '' ? (int)$data['DurationMinutes'] : null,
            'languageCode'              => $data['LanguageCode'] ?? null,
            'minAge'                    => $data['MinAge'] ?? null,
            'maxAge'                    => isset($data['MaxAge']) && $data['MaxAge'] !== '' ? (int)$data['MaxAge'] : null,
            'reservationRequired'       => isset($data['ReservationRequired']) ? 1 : 0,
            'isFree'                    => isset($data['IsFree']) ? 1 : 0,
            'notes'                     => $data['Notes'] ?? '',
            'historyTicketLabel'        => $data['HistoryTicketLabel'] ?? null,
            'ctaLabel'                  => $data['CtaLabel'] ?? null,
            'ctaUrl'                    => $data['CtaUrl'] ?? null,
            'isCancelled'               => isset($data['IsCancelled']) ? 1 : 0,
            'isActive'                  => isset($data['IsActive']) ? 1 : 0,
        ]);
    }

    /**
     * Hard-deletes a session row. Will fail if order items reference this session.
     */
    public function delete(int $sessionId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM EventSession WHERE EventSessionId = :sessionId');
        return $stmt->execute(['sessionId' => $sessionId]);
    }

    /**
     * Returns distinct session dates matching the base filters (ignoring user-facing schedule filters).
     * Used to build the day filter UI with all available days, even when other filters narrow results.
     *
     * @return ScheduleDayData[]
     */
    public function findDistinctDays(EventSessionFilter $filter): array
    {
        $conditions = [];
        $params = [];

        if ($filter->eventTypeId !== null) {
            $conditions[] = 'e.EventTypeId = :eventTypeId';
            $params['eventTypeId'] = $filter->eventTypeId;
        }

        if ($filter->isActive !== null) {
            $conditions[] = 'es.IsActive = :isActive';
            $params['isActive'] = $filter->isActive ? 1 : 0;
        }

        if ($filter->eventIsActive !== null) {
            $conditions[] = 'e.IsActive = :eventIsActive';
            $params['eventIsActive'] = $filter->eventIsActive ? 1 : 0;
        }

        $conditions[] = 'es.IsCancelled = 0';

        if ($filter->eventId !== null) {
            $conditions[] = 'es.EventId = :eventId';
            $params['eventId'] = $filter->eventId;
        }

        if ($filter->visibleDays !== null && $filter->visibleDays !== []) {
            if (count($filter->visibleDays) < 7) {
                $dayParams = [];
                foreach (array_values($filter->visibleDays) as $index => $day) {
                    $key = 'visDay' . $index;
                    $dayParams[] = ':' . $key;
                    $params[$key] = (int) $day;
                }
                $conditions[] = 'DAYOFWEEK(es.StartDateTime) - 1 IN (' . implode(',', $dayParams) . ')';
            }
        }

        $whereClause = $conditions !== [] ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $sql = "
            SELECT DISTINCT DATE(es.StartDateTime) AS date, DAYNAME(es.StartDateTime) AS dayName
            FROM EventSession es
            INNER JOIN Event e ON es.EventId = e.EventId
            {$whereClause}
            ORDER BY date ASC
        ";

        $maxDays = $filter->maxDays ?? 7;
        $sql .= ' LIMIT ' . (int) $maxDays;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return array_map([ScheduleDayData::class, 'fromRow'], $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * Converts an English day name to MySQL DAYOFWEEK() numbering (1=Sunday through 7=Saturday).
     *
     * @return int|null Null if the day name is not recognized.
     */
    private function dayNameToNumber(string $dayName): ?int
    {
        $map = [
            'sunday' => 1, 'monday' => 2, 'tuesday' => 3, 'wednesday' => 4,
            'thursday' => 5, 'friday' => 6, 'saturday' => 7,
        ];
        return $map[strtolower($dayName)] ?? null;
    }
}
