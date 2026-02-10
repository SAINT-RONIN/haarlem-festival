<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

/**
 * Interface for EventSessionLabel repository operations.
 */
interface IEventSessionLabelRepository
{
    /**
     * Find all labels for a session.
     *
     * @param int $sessionId
     * @return array Array of label rows
     */
    public function findBySessionId(int $sessionId): array;

    /**
     * Find all labels for multiple sessions.
     *
     * @param array<int> $sessionIds
     * @return array Array of label rows keyed by session ID
     */
    public function findBySessionIds(array $sessionIds): array;

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

