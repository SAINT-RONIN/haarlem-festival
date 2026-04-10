<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\EventSessionLabel;
use App\DTOs\Domain\Filters\EventSessionRelatedFilter;
use App\Repositories\Interfaces\IEventSessionLabelRepository;

// Free-text tags attached to sessions (e.g. "Sold Out", "Last Tickets", "New").
class EventSessionLabelRepository extends BaseRepository implements IEventSessionLabelRepository
{
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

    /** @return array<int, EventSessionLabel[]> keyed by EventSessionId */
    public function findLabelsBySessionIds(array $sessionIds): array
    {
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

    public function create(int $sessionId, string $labelText): int
    {
        return $this->executeInsert(
            'INSERT INTO EventSessionLabel (EventSessionId, LabelText)
            VALUES (:sessionId, :labelText)',
            ['sessionId' => $sessionId, 'labelText' => $labelText],
        );
    }

    public function delete(int $labelId): bool
    {
        $this->execute(
            'DELETE FROM EventSessionLabel WHERE EventSessionLabelId = :labelId',
            ['labelId' => $labelId],
        );

        return true;
    }

    // Delete-all-then-re-create pattern for admin edits.
    public function deleteAllForSession(int $sessionId): bool
    {
        $this->execute(
            'DELETE FROM EventSessionLabel WHERE EventSessionId = :sessionId',
            ['sessionId' => $sessionId],
        );

        return true;
    }

    public function countBySession(int $sessionId): int
    {
        $stmt = $this->execute(
            'SELECT COUNT(*) FROM EventSessionLabel WHERE EventSessionId = :sessionId',
            ['sessionId' => $sessionId],
        );

        return (int) $stmt->fetchColumn();
    }
}
