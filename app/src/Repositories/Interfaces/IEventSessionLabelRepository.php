<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\EventSessionLabel;
use App\Models\EventSessionLabelFilter;

/**
 * Interface for EventSessionLabel repository operations.
 */
interface IEventSessionLabelRepository
{
    /**
     * Find labels using optional filters.
     *
     * @param EventSessionLabelFilter|array<string, mixed> $filters
     * @return EventSessionLabel[]|array<int, EventSessionLabel[]>
     */
    public function findLabels(EventSessionLabelFilter|array $filters = new EventSessionLabelFilter()): array;

    /**
     * Create a new label for a session.
     *
     * @param int $sessionId
     * @param string $labelText
     * @return int The new label ID
     */
    public function create(int $sessionId, string $labelText): int;

    /**
     * Delete a label by ID.
     *
     * @param int $labelId
     * @return bool Success status
     */
    public function delete(int $labelId): bool;

    /**
     * Delete all labels for a session.
     *
     * @param int $sessionId
     * @return bool Success status
     */
    public function deleteAllForSession(int $sessionId): bool;

    /**
     * Count labels for a session.
     *
     * @param int $sessionId
     * @return int Number of labels
     */
    public function countBySession(int $sessionId): int;
}
