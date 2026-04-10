<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\EventSessionLabel;
use App\DTOs\Domain\Filters\EventSessionRelatedFilter;

/**
 * Contract for managing free-text labels (tags/badges) attached to event sessions.
 * Labels like "Sold Out", "Last Tickets", or "New" are displayed on session cards
 * in the public schedule. Supports batch retrieval grouped by session ID for list views.
 */
interface IEventSessionLabelRepository
{
    /**
     * Retrieves labels with optional filtering by session ID.
     *
     * @return EventSessionLabel[]
     */
    public function findLabels(EventSessionRelatedFilter $filters = new EventSessionRelatedFilter()): array;

    /**
     * Batch-fetches labels for multiple sessions in one query, grouped by session ID.
     * Used to efficiently attach label badges when rendering session lists.
     *
     * @param int[] $sessionIds
     * @return array<int, EventSessionLabel[]> Keyed by EventSessionId.
     */
    public function findLabelsBySessionIds(array $sessionIds): array;

    /**
     * Attaches a new label to a session.
     *
     * @return int The auto-incremented EventSessionLabelId.
     */
    public function create(int $sessionId, string $labelText): int;

    /**
     * Removes a single label by its primary key.
     */
    public function delete(int $labelId): bool;

    /**
     * Removes all labels from a session. Used during the delete-all-then-recreate
     * pattern when an admin edits a session's label set.
     */
    public function deleteAllForSession(int $sessionId): bool;

    /**
     * Returns the number of labels attached to a session.
     */
    public function countBySession(int $sessionId): int;
}
