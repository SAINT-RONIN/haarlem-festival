<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Cms\EventSessionUpsertData;
use App\Enums\PriceTierId;
use App\DTOs\Filters\EventSessionFilter;
use App\DTOs\Schedule\ScheduleDayData;
use App\DTOs\Events\SessionCapacityInfo;
use App\DTOs\Schedule\SessionQueryResult;
use App\DTOs\Schedule\SessionWithEvent;
use App\Exceptions\RepositoryException;
use App\Repositories\Interfaces\IEventSessionRepository;
use PDO;

/**
 * Queries and manages rows in the EventSession table. Supports heavily-filtered session
 * listing with joins to Event, EventType, Venue, Artist, and MediaAsset. Provides both
 * flat session lists and day-grouped results for the public schedule UI.
 */
class EventSessionRepository extends BaseRepository implements IEventSessionRepository
{

    /**
     * Builds and executes a dynamic query for event sessions with extensive filter support.
     * When groupByDay is enabled, runs a two-pass query: first fetches distinct dates,
     * then retrieves sessions only within those dates.
     *
     * @return SessionQueryResult Contains sessions and optionally day metadata.
     */
    public function findSessions(EventSessionFilter $filters = new EventSessionFilter()): SessionQueryResult
    {
        [$conditions, $params] = $this->buildFilterConditions($filters);

        $whereClause = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);
        $orderBy = $this->resolveOrderBy($filters->orderBy);

        $groupByDay = (bool)($filters->groupByDay ?? false);

        if ($groupByDay) {
            return $this->executeGroupedDayQuery($filters, $whereClause, $params, $orderBy);
        }

        return $this->executeFlatQuery($filters, $whereClause, $params, $orderBy);
    }

    /**
     * Builds all WHERE conditions and bound parameters from the filter DTO.
     *
     * @return array{0: string[], 1: array<string, mixed>}
     */
    private function buildFilterConditions(EventSessionFilter $filters): array
    {
        $conditions = [];
        $params = [];

        $this->addIdentityFilters($filters, $conditions, $params);
        $this->addStatusFilters($filters, $conditions, $params);
        $this->addDateFilters($filters, $conditions, $params);
        $this->addVisibleDaysFilter($filters, $conditions, $params);
        $this->addTimeRangeFilter($filters, $conditions);
        $this->addPriceTypeFilter($filters, $conditions, $params);
        $this->addTextFilters($filters, $conditions, $params);

        return [$conditions, $params];
    }

    /** Adds event ID, event type, session ID, and session ID list conditions. */
    private function addIdentityFilters(EventSessionFilter $filters, array &$conditions, array &$params): void
    {
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

        if ($filters->sessionIds !== null && $filters->sessionIds !== []) {
            $sessionIdPlaceholders = [];
            foreach ($filters->sessionIds as $index => $sid) {
                $key = 'sessionId_' . $index;
                $sessionIdPlaceholders[] = ':' . $key;
                $params[$key] = (int)$sid;
            }
            $conditions[] = 'es.EventSessionId IN (' . implode(',', $sessionIdPlaceholders) . ')';
        }
    }

    /** Adds active/cancelled/event-active status conditions. */
    private function addStatusFilters(EventSessionFilter $filters, array &$conditions, array &$params): void
    {
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
    }

    /** Adds start/end date range and day-of-week conditions. */
    private function addDateFilters(EventSessionFilter $filters, array &$conditions, array &$params): void
    {
        if ($filters->startDate !== null) {
            $conditions[] = 'DATE(es.StartDateTime) >= :startDate';
            $params['startDate'] = $filters->startDate;
        }

        if ($filters->endDate !== null) {
            $conditions[] = 'DATE(es.StartDateTime) <= :endDate';
            $params['endDate'] = $filters->endDate;
        }

        if ($filters->dayOfWeekNumber !== null) {
            $conditions[] = 'DAYOFWEEK(es.StartDateTime) = :dayOfWeekNum';
            $params['dayOfWeekNum'] = $filters->dayOfWeekNumber;
        }
    }

    /** Restricts results to specific weekdays (0=Sunday..6=Saturday). */
    private function addVisibleDaysFilter(EventSessionFilter $filters, array &$conditions, array &$params): void
    {
        $visibleDays = $filters->visibleDays;

        if (!is_array($visibleDays)) {
            return;
        }

        if ($visibleDays === []) {
            $conditions[] = '1 = 0';
            return;
        }

        if (count($visibleDays) < 7) {
            $dayParams = [];
            foreach (array_values($visibleDays) as $index => $day) {
                $key = 'visibleDay' . $index;
                $dayParams[] = ':' . $key;
                $params[$key] = (int)$day;
            }
            $conditions[] = 'DAYOFWEEK(es.StartDateTime) - 1 IN (' . implode(',', $dayParams) . ')';
        }
    }

    /** Adds morning/afternoon/evening time range condition. */
    private function addTimeRangeFilter(EventSessionFilter $filters, array &$conditions): void
    {
        if ($filters->timeRange === null) {
            return;
        }

        match ($filters->timeRange) {
            'morning'   => $conditions[] = 'HOUR(es.StartDateTime) < 12',
            'afternoon' => $conditions[] = '(HOUR(es.StartDateTime) >= 12 AND HOUR(es.StartDateTime) < 17)',
            'evening'   => $conditions[] = 'HOUR(es.StartDateTime) >= 17',
        };
    }

    /** Classifies sessions as free, fixed-price, or pay-what-you-like via subqueries. */
    private function addPriceTypeFilter(EventSessionFilter $filters, array &$conditions, array &$params): void
    {
        if ($filters->priceType === null) {
            return;
        }

        match ($filters->priceType) {
            'free' => $conditions[] = '(es.IsFree = 1 OR NOT EXISTS (SELECT 1 FROM EventSessionPrice esp WHERE esp.EventSessionId = es.EventSessionId AND esp.Price > 0))',
            'pay-what-you-like' => $this->addPwylCondition($conditions, $params),
            'fixed' => $this->addFixedCondition($conditions, $params),
            default => null,
        };
    }

    private function addPwylCondition(array &$conditions, array &$params): void
    {
        $params['pwylTierId'] = PriceTierId::PayWhatYouLike->value;
        $conditions[] = 'EXISTS (SELECT 1 FROM EventSessionPrice esp WHERE esp.EventSessionId = es.EventSessionId AND esp.PriceTierId = :pwylTierId)';
    }

    private function addFixedCondition(array &$conditions, array &$params): void
    {
        $params['pwylTierId'] = PriceTierId::PayWhatYouLike->value;
        $conditions[] = 'EXISTS (SELECT 1 FROM EventSessionPrice esp WHERE esp.EventSessionId = es.EventSessionId AND esp.Price > 0 AND esp.PriceTierId != :pwylTierId)';
    }

    /** Adds venue name, language code, and minimum age conditions. */
    private function addTextFilters(EventSessionFilter $filters, array &$conditions, array &$params): void
    {
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
    }

    /** Whitelist-based ORDER BY to prevent SQL injection via user-supplied sort values. */
    private function resolveOrderBy(?string $requestedOrderBy): string
    {
        $allowed = [
            'es.StartDateTime ASC',
            'es.StartDateTime DESC',
            'DATE(es.StartDateTime) ASC, es.StartDateTime ASC',
        ];

        $value = is_string($requestedOrderBy) ? $requestedOrderBy : '';

        return in_array($value, $allowed, true) ? $value : 'es.StartDateTime ASC';
    }

    /** Returns the shared FROM clause with all necessary JOINs. */
    private function getBaseFromClause(): string
    {
        return '
            FROM EventSession es
            INNER JOIN Event e ON es.EventId = e.EventId
            INNER JOIN EventType et ON e.EventTypeId = et.EventTypeId
            LEFT JOIN Venue v ON e.VenueId = v.VenueId
            LEFT JOIN Artist a ON e.ArtistId = a.ArtistId
            LEFT JOIN MediaAsset ma ON a.ImageAssetId = ma.MediaAssetId
        ';
    }

    /** Returns the standard SELECT columns for session queries. */
    private function getSessionSelectColumns(): string
    {
        return '
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
        ';
    }

    /**
     * Two-pass grouped query: fetches distinct dates first, then sessions within those dates.
     * Powers the paginated day-tab schedule UI.
     */
    private function executeGroupedDayQuery(EventSessionFilter $filters, string $whereClause, array $params, string $orderBy): SessionQueryResult
    {
        $maxDays = max(1, (int)$filters->maxDays);
        $baseFrom = $this->getBaseFromClause();

        $days = $this->fetchDistinctDates($baseFrom, $whereClause, $params, $maxDays);

        if ($days === []) {
            return new SessionQueryResult(sessions: [], days: []);
        }

        $sessions = $this->fetchSessionsForDates($days, $baseFrom, $whereClause, $params, $orderBy, $filters->limit);

        return new SessionQueryResult(
            sessions: array_map([SessionWithEvent::class, 'fromRow'], $sessions),
            days: array_map([ScheduleDayData::class, 'fromRow'], $days),
        );
    }

    /** Fetches distinct session dates matching the current filters. */
    private function fetchDistinctDates(string $baseFrom, string $whereClause, array $params, int $maxDays): array
    {
        $sql = 'SELECT DISTINCT DATE(es.StartDateTime) AS Date ' . $baseFrom . ' ' . $whereClause . ' ORDER BY Date ASC LIMIT :maxDays';

        try {
            $stmt = $this->pdo->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            $stmt->bindValue(':maxDays', $maxDays, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $error) {
            throw new RepositoryException('Failed to fetch distinct session dates.', (int) $error->getCode(), $error);
        }
    }

    /** Fetches sessions limited to the given set of dates. */
    private function fetchSessionsForDates(array $days, string $baseFrom, string $whereClause, array $params, string $orderBy, ?int $limit): array
    {
        $dates = array_column($days, 'Date');
        $dateBindings = [];
        $datePlaceholders = [];

        foreach ($dates as $index => $date) {
            $placeholder = 'sessionDate' . $index;
            $datePlaceholders[] = ':' . $placeholder;
            $dateBindings[$placeholder] = $date;
        }

        $dateCondition = 'DATE(es.StartDateTime) IN (' . implode(',', $datePlaceholders) . ')';
        $fullWhere = $whereClause === '' ? 'WHERE ' . $dateCondition : $whereClause . ' AND ' . $dateCondition;

        $sql = $this->getSessionSelectColumns() . ' ' . $baseFrom . ' ' . $fullWhere . ' ORDER BY ' . $orderBy;

        if ($limit !== null && $limit > 0) {
            $sql .= ' LIMIT ' . (int)$limit;
        }

        $stmt = $this->execute($sql, array_merge($params, $dateBindings));

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Executes a simple flat session query (no day grouping). */
    private function executeFlatQuery(EventSessionFilter $filters, string $whereClause, array $params, string $orderBy): SessionQueryResult
    {
        $sql = $this->getSessionSelectColumns() . ' ' . $this->getBaseFromClause() . ' ' . $whereClause . ' ORDER BY ' . $orderBy;

        if ($filters->limit !== null && $filters->limit > 0) {
            $sql .= ' LIMIT ' . (int)$filters->limit;
        }

        $stmt = $this->execute($sql, $params);

        return new SessionQueryResult(
            sessions: array_map([SessionWithEvent::class, 'fromRow'], $stmt->fetchAll(PDO::FETCH_ASSOC)),
        );
    }

    /**
     * Inserts a new session from the typed CMS upsert payload.
     *
     * @return int The auto-incremented EventSessionId.
     */
    public function create(EventSessionUpsertData $data): int
    {
        return $this->executeInsert(
            'INSERT INTO EventSession (
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
                :isCancelled, :isActive
            )',
            [
                'eventId' => $data->eventId,
                'startDateTime' => $data->startDateTime,
                'endDateTime' => $data->endDateTime,
                'capacityTotal' => $data->capacityTotal,
                'capacitySingleTicketLimit' => $data->capacitySingleTicketLimit,
                'hallName' => $data->hallName,
                'sessionType' => $data->sessionType,
                'durationMinutes' => $data->durationMinutes,
                'languageCode' => $data->languageCode,
                'minAge' => $data->minAge,
                'maxAge' => $data->maxAge,
                'reservationRequired' => $data->reservationRequired ? 1 : 0,
                'isFree' => $data->isFree ? 1 : 0,
                'notes' => $data->notes,
                'historyTicketLabel' => $data->historyTicketLabel,
                'ctaLabel' => $data->ctaLabel,
                'ctaUrl' => $data->ctaUrl,
                'isCancelled' => $data->isCancelled ? 1 : 0,
                'isActive' => $data->isActive ? 1 : 0,
            ],
        );
    }

    /**
     * Updates all mutable session fields from the typed CMS upsert payload.
     */
    public function update(int $sessionId, EventSessionUpsertData $data): bool
    {
        $this->execute(
            'UPDATE EventSession SET
                StartDateTime = :startDateTime, EndDateTime = :endDateTime,
                CapacityTotal = :capacityTotal, CapacitySingleTicketLimit = :capacitySingleTicketLimit,
                HallName = :hallName, SessionType = :sessionType,
                DurationMinutes = :durationMinutes, LanguageCode = :languageCode,
                MinAge = :minAge, MaxAge = :maxAge,
                ReservationRequired = :reservationRequired, IsFree = :isFree,
                Notes = :notes, HistoryTicketLabel = :historyTicketLabel,
                CtaLabel = :ctaLabel, CtaUrl = :ctaUrl,
                IsCancelled = :isCancelled, IsActive = :isActive
            WHERE EventSessionId = :sessionId',
            [
                'sessionId'                 => $sessionId,
                'startDateTime'             => $data->startDateTime,
                'endDateTime'               => $data->endDateTime,
                'capacityTotal'             => $data->capacityTotal,
                'capacitySingleTicketLimit' => $data->capacitySingleTicketLimit,
                'hallName'                  => $data->hallName,
                'sessionType'               => $data->sessionType,
                'durationMinutes'           => $data->durationMinutes,
                'languageCode'              => $data->languageCode,
                'minAge'                    => $data->minAge,
                'maxAge'                    => $data->maxAge,
                'reservationRequired'       => $data->reservationRequired ? 1 : 0,
                'isFree'                    => $data->isFree ? 1 : 0,
                'notes'                     => $data->notes,
                'historyTicketLabel'        => $data->historyTicketLabel,
                'ctaLabel'                  => $data->ctaLabel,
                'ctaUrl'                    => $data->ctaUrl,
                'isCancelled'               => $data->isCancelled ? 1 : 0,
                'isActive'                  => $data->isActive ? 1 : 0,
            ],
        );

        return true;
    }

    /**
     * Hard-deletes a session row. Will fail if order items reference this session.
     */
    public function delete(int $sessionId): bool
    {
        $this->execute(
            'DELETE FROM EventSession WHERE EventSessionId = :sessionId',
            ['sessionId' => $sessionId],
        );

        return true;
    }

    /**
     * Returns distinct session dates matching the base filters (ignoring user-facing schedule filters).
     * Used to build the day filter UI with all available days, even when other filters narrow results.
     *
     * @return ScheduleDayData[]
     */
    public function findDistinctDays(EventSessionFilter $filter): array
    {
        $params = [];
        $conditions = $this->buildDistinctDayConditions($filter, $params);
        $sql = $this->buildDistinctDayQuery($conditions, (int)$filter->maxDays);

        $stmt = $this->execute($sql, $params);

        return array_map([ScheduleDayData::class, 'fromRow'], $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * @param array<string,mixed> $params
     * @return string[]
     */
    private function buildDistinctDayConditions(EventSessionFilter $filter, array &$params): array
    {
        $conditions = ['es.IsCancelled = 0'];

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

        if ($filter->eventId !== null) {
            $conditions[] = 'es.EventId = :eventId';
            $params['eventId'] = $filter->eventId;
        }

        $this->appendVisibleDayCondition($conditions, $params, $filter->visibleDays);

        return $conditions;
    }

    /**
     * @param string[] $conditions
     * @param array<string,mixed> $params
     * @param int[]|null $visibleDays
     */
    private function appendVisibleDayCondition(array &$conditions, array &$params, ?array $visibleDays): void
    {
        if ($visibleDays === null || $visibleDays === [] || count($visibleDays) >= 7) {
            return;
        }

        $dayParams = [];

        foreach (array_values($visibleDays) as $index => $day) {
            $key = 'visDay' . $index;
            $dayParams[] = ':' . $key;
            $params[$key] = (int)$day;
        }

        $conditions[] = 'DAYOFWEEK(es.StartDateTime) - 1 IN (' . implode(',', $dayParams) . ')';
    }

    /**
     * @param string[] $conditions
     */
    private function buildDistinctDayQuery(array $conditions, int $maxDays): string
    {
        $whereClause = $conditions !== [] ? 'WHERE ' . implode(' AND ', $conditions) : '';

        return "
            SELECT DISTINCT DATE(es.StartDateTime) AS date, DAYNAME(es.StartDateTime) AS dayName
            FROM EventSession es
            INNER JOIN Event e ON es.EventId = e.EventId
            {$whereClause}
            ORDER BY date ASC
            LIMIT {$maxDays}
        ";
    }

    /**
     * Bulk-deactivates all sessions belonging to an event. Called when the parent event
     * is deactivated to ensure no sessions remain bookable.
     */
    public function deactivateByEventId(int $eventId): bool
    {
        $this->execute(
            'UPDATE EventSession SET IsActive = 0 WHERE EventId = :eventId',
            ['eventId' => $eventId],
        );

        return true;
    }

    /**
     * Returns the number of remaining seats for a session.
     * Computes available = CapacityTotal - SoldSingleTickets - SoldReservedSeats.
     */
    public function getAvailableSeats(int $sessionId): int
    {
        // Remaining seats = total capacity minus all sold tickets (single + reserved)
        $stmt = $this->execute(
            'SELECT (CapacityTotal - SoldSingleTickets - SoldReservedSeats) AS available
            FROM EventSession
            WHERE EventSessionId = :sessionId',
            [':sessionId' => $sessionId],
        );
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? max(0, (int)$row['available']) : 0;
    }

    /**
     * Atomically increments SoldSingleTickets for a session. The WHERE condition
     * ensures the update only applies when enough capacity remains, preventing
     * overselling under concurrent checkouts.
     *
     * @return bool True if the reservation succeeded, false if insufficient capacity.
     */
    public function decrementCapacity(int $sessionId, int $quantity): bool
    {
        // Atomic seat reservation — only succeeds if enough capacity remains
        $stmt = $this->execute(
            'UPDATE EventSession
            SET SoldSingleTickets = SoldSingleTickets + :quantity
            WHERE EventSessionId = :sessionId
              AND (CapacityTotal - SoldSingleTickets - SoldReservedSeats) >= :quantityCheck',
            [':sessionId' => $sessionId, ':quantity' => $quantity, ':quantityCheck' => $quantity],
        );

        return $stmt->rowCount() > 0;
    }

    /**
     * Returns capacity and ticket-sale counts for a session.
     * Used for pre-checkout validation including the single-ticket cap.
     */
    public function getCapacityInfo(int $sessionId): ?SessionCapacityInfo
    {
        // Fetch the capacity snapshot for a single session
        return $this->fetchOne(
            'SELECT EventSessionId, CapacityTotal, SoldSingleTickets, SoldReservedSeats
            FROM EventSession
            WHERE EventSessionId = :sessionId',
            [':sessionId' => $sessionId],
            fn(array $row) => SessionCapacityInfo::fromRow($row),
        );
    }

    /**
     * Restores reserved capacity when an order is cancelled or expires.
     * Decrements SoldSingleTickets by the given quantity.
     */
    public function restoreCapacity(int $sessionId, int $quantity): void
    {
        // Restore seats that were previously reserved for a cancelled/expired order
        $this->execute(
            'UPDATE EventSession
            SET SoldSingleTickets = GREATEST(0, SoldSingleTickets - :quantity)
            WHERE EventSessionId = :sessionId',
            [':sessionId' => $sessionId, ':quantity' => $quantity],
        );
    }
}
