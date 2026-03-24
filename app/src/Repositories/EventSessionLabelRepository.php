<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\EventSessionLabel;
use App\Models\EventSessionRelatedFilter;
use App\Repositories\Interfaces\IEventSessionLabelRepository;
use PDO;

/**
 * Manages the EventSessionLabel table, which stores free-text tags attached to sessions
 * (e.g. "Sold Out", "Last Tickets", "New"). Labels are displayed on session cards in the
 * public schedule. Supports batch retrieval grouped by session ID for list views.
 */
class EventSessionLabelRepository implements IEventSessionLabelRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

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

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([EventSessionLabel::class, 'fromRow'], $rows);
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

        // Build numbered placeholders (:sessionId0, :sessionId1, ...) for the IN clause
        $params = [];
        $inPlaceholders = [];
        foreach ($normalizedIds as $index => $sessionId) {
            $paramName = 'sessionId' . $index;
            $inPlaceholders[] = ':' . $paramName;
            $params[$paramName] = $sessionId;
        }

        $sql = '
            SELECT EventSessionLabelId, EventSessionId, LabelText
            FROM EventSessionLabel
            WHERE EventSessionId IN (' . implode(', ', $inPlaceholders) . ')
            ORDER BY EventSessionId ASC, EventSessionLabelId ASC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $labels = array_map([EventSessionLabel::class, 'fromRow'], $rows);

        $grouped = [];
        foreach ($labels as $label) {
            $grouped[$label->eventSessionId][] = $label;
        }

        return $grouped;
    }

    /**
     * Attaches a new label to a session.
     *
     * @return int The auto-incremented EventSessionLabelId.
     * @inheritDoc
     */
    public function create(int $sessionId, string $labelText): int
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO EventSessionLabel (EventSessionId, LabelText)
            VALUES (:sessionId, :labelText)
        ');
        $stmt->execute([
            'sessionId' => $sessionId,
            'labelText' => $labelText,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * @inheritDoc
     */
    public function delete(int $labelId): bool
    {
        $stmt = $this->pdo->prepare('
            DELETE FROM EventSessionLabel
            WHERE EventSessionLabelId = :labelId
        ');

        return $stmt->execute(['labelId' => $labelId]);
    }

    /**
     * Removes all labels from a session. Useful when replacing the full label set during
     * an admin edit (delete-all then re-create pattern).
     *
     * @inheritDoc
     */
    public function deleteAllForSession(int $sessionId): bool
    {
        $stmt = $this->pdo->prepare('
            DELETE FROM EventSessionLabel
            WHERE EventSessionId = :sessionId
        ');

        return $stmt->execute(['sessionId' => $sessionId]);
    }

    /**
     * @inheritDoc
     */
    public function countBySession(int $sessionId): int
    {
        $stmt = $this->pdo->prepare('
            SELECT COUNT(*) FROM EventSessionLabel
            WHERE EventSessionId = :sessionId
        ');
        $stmt->execute(['sessionId' => $sessionId]);

        return (int)$stmt->fetchColumn();
    }
}
