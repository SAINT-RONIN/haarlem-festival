<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Infrastructure\Database;
use App\Models\EventSessionLabel;
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
     * Find all labels for a session.
     *
     * @param int $sessionId
     * @return EventSessionLabel[]
     */
    public function findBySessionId(int $sessionId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT EventSessionLabelId, EventSessionId, LabelText
            FROM EventSessionLabel
            WHERE EventSessionId = :sessionId
            ORDER BY EventSessionLabelId ASC
        ');
        $stmt->execute(['sessionId' => $sessionId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map([EventSessionLabel::class, 'fromRow'], $rows);
    }

    /**
     * Find all labels for multiple sessions.
     *
     * @param array<int> $sessionIds
     * @return array<int, EventSessionLabel[]>
     */
    public function findBySessionIds(array $sessionIds): array
    {
        if (empty($sessionIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($sessionIds), '?'));
        $stmt = $this->pdo->prepare("
            SELECT EventSessionLabelId, EventSessionId, LabelText
            FROM EventSessionLabel
            WHERE EventSessionId IN ($placeholders)
            ORDER BY EventSessionId, EventSessionLabelId ASC
        ");
        $stmt->execute($sessionIds);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group by session ID
        $grouped = [];
        foreach ($rows as $row) {
            $sid = (int)$row['EventSessionId'];
            if (!isset($grouped[$sid])) {
                $grouped[$sid] = [];
            }
            $grouped[$sid][] = EventSessionLabel::fromRow($row);
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
