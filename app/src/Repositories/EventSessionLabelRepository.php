<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\EventSessionLabel;
use App\Models\EventSessionLabelFilter;
use App\Repositories\Interfaces\IEventSessionLabelRepository;
use PDO;

/**
 * Repository for EventSessionLabel database operations.
 */
class EventSessionLabelRepository implements IEventSessionLabelRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * @return EventSessionLabel[]
     */
    public function findLabels(EventSessionLabelFilter $filters = new EventSessionLabelFilter()): array
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
     * @param int[] $sessionIds
     * @return array<int, EventSessionLabel[]>
     */
    public function findLabelsBySessionIds(array $sessionIds): array
    {
        $normalizedIds = array_values(array_unique(array_map('intval', $sessionIds)));
        if ($normalizedIds === []) {
            return [];
        }

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
