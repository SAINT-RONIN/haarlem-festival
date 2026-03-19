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

    public function findLabels(EventSessionLabelFilter|array $filters = new EventSessionLabelFilter()): array
    {
        if (is_array($filters)) {
            $filters = EventSessionLabelFilter::fromArray($filters);
        }

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

        $sessionIds = $filters->sessionIds;
        if (is_array($sessionIds)) {
            $normalizedIds = array_values(array_unique(array_map('intval', $sessionIds)));
            if ($normalizedIds === []) {
                return [];
            }

            $inPlaceholders = [];
            foreach ($normalizedIds as $index => $sessionId) {
                $paramName = 'sessionId' . $index;
                $inPlaceholders[] = ':' . $paramName;
                $params[$paramName] = $sessionId;
            }

            $sql .= ' AND EventSessionId IN (' . implode(', ', $inPlaceholders) . ')';
        }

        $sql .= ' ORDER BY EventSessionId ASC, EventSessionLabelId ASC';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $labels = array_map([EventSessionLabel::class, 'fromRow'], $rows);

        $groupBySession = (bool)($filters->groupBySession ?? false);
        if (!$groupBySession) {
            return $labels;
        }

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
