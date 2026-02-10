<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

/**
 * Interface for Event repository operations.
 */
interface IEventRepository
{
    /**
     * Find all events by event type.
     *
     * @param int $eventTypeId
     * @return array Array of event rows
     */
    public function findAllByType(int $eventTypeId): array;

    /**
     * Find all events with type and venue info.
     *
     * @return array Array of event rows with joins
     */
    public function findAllWithDetails(): array;

    /**
     * Find an event by ID.
     *
     * @param int $eventId
     * @return array|null Event row or null
     */
    public function findById(int $eventId): ?array;

    /**
     * Find an event by ID with related details.
     *
     * @param int $eventId
     * @return array|null Event row with venue/type or null
     */
    public function findByIdWithDetails(int $eventId): ?array;

    /**
     * Create a new event.
     *
     * @param array $data Event data
     * @return int The new event ID
     */
    public function create(array $data): int;

    /**
     * Update an event.
     *
     * @param int $eventId
     * @param array $data Event data
     * @return bool Success status
     */
    public function update(int $eventId, array $data): bool;

    /**
     * Delete an event.
     *
     * @param int $eventId
     * @return bool Success status
     */
    public function delete(int $eventId): bool;
}

