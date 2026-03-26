<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\EventSessionLabel;
use App\DTOs\Filters\EventSessionRelatedFilter;
use App\Repositories\Interfaces\IEventSessionLabelRepository;

/**
 * Manages the EventSessionLabel table, which stores free-text tags attached to sessions
 * (e.g. "Sold Out", "Last Tickets", "New"). Labels are displayed on session cards in the
 * public schedule. Supports batch retrieval grouped by session ID for list views.
 */
class EventSessionLabelRepository extends BaseRepository implements IEventSessionLabelRepository
{
    /**
     * Retrieves labels with optional filtering by session ID.
     *
     * @return EventSessionLabel[] Ordered by session then label ID. Empty array if no matches.
     */
    public function findLabels(EventSessionRelatedFilter $filters = new EventSessionRelatedFilter()): array
    {
        $sql = '
            SELECT EventSessionLabelId, EventSessionId, LabelText
            FROM EventSessionLabel
            WHERE 1 = 1
        ';
        $params = [];

        if ($filters->sessionId !== null) {
            $sql .= ' AND EventSessionId = :sessionId';
            $params['sessionId'] = $filters->sessionId;
        }

        $sql .= ' ORDER BY EventSessionId ASC, EventSessionLabelId ASC';

        return $this->fetchAll($sql, $params, fn(array $row) => EventSessionLabel::fromRow($row));
    }

    /**
     * Batch-fetches labels for multiple sessions in a single query, then groups them
     * by session ID. Used to efficiently attach label badges when rendering session lists.
     *
     * @param int[] $sessionIds
     * @return array<int, EventSessionLabel[]> Keyed by EventSessionId. Missing IDs are absent (not empty arrays).
     */
    public function findLabelsBySessionIds(array $sessionIds): array
    {
        // Deduplicate and cast IDs to int before building the IN clause
        $normalizedIds = array_values(array_unique(array_map('intval', $sessionIds)));
        if ($normalizedIds === []) {
            return [];
        }

        $inClause = $this->buildInClause($normalizedIds, 'sessionId');

        $sql = '
            SELECT EventSessionLabelId, EventSessionId, LabelText
            FROM EventSessionLabel
            WHERE EventSessionId IN (' . $inClause['placeholders'] . ')
            ORDER BY EventSessionId ASC, EventSessionLabelId ASC
        ';

        $labels = $this->fetchAll($sql, $inClause['params'], fn(array $row) => EventSessionLabel::fromRow($row));

        return $this->groupByKey($labels, 'eventSessionId');
    }

    /**
     * Attaches a new label to a session.
     *
     * @return int The auto-incremented EventSessionLabelId.
     * @inheritDoc
     */
    public function create(int $sessionId, string $labelText): int
    {
        return $this->executeInsert(
            'INSERT INTO EventSessionLabel (EventSessionId, LabelText)
            VALUES (:sessionId, :labelText)',
            ['sessionId' => $sessionId, 'labelText' => $labelText],
        );
    }

    /**
     * @inheritDoc
     */
    public function delete(int $labelId): bool
    {
        $this->execute(
            'DELETE FROM EventSessionLabel WHERE EventSessionLabelId = :labelId',
            ['labelId' => $labelId],
        );

        return true;
    }

    /**
     * Removes all labels from a session. Useful when replacing the full label set during
     * an admin edit (delete-all then re-create pattern).
     *
     * @inheritDoc
     */
    public function deleteAllForSession(int $sessionId): bool
    {
        $this->execute(
            'DELETE FROM EventSessionLabel WHERE EventSessionId = :sessionId',
            ['sessionId' => $sessionId],
        );

        return true;
    }

    /**
     * @inheritDoc
     */
    public function countBySession(int $sessionId): int
    {
        $stmt = $this->execute(
            'SELECT COUNT(*) FROM EventSessionLabel WHERE EventSessionId = :sessionId',
            ['sessionId' => $sessionId],
        );

        return (int)$stmt->fetchColumn();
    }
}
