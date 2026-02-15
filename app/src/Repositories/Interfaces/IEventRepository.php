<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Event;

/**
 * Interface for Event repository operations.
 */
interface IEventRepository
{
    /**
     * Find all events by event type.
     *
     * @param int $eventTypeId
     * @return array<int, array{
     *     EventId: int,
     *     EventTypeId: int,
     *     Title: string,
     *     ShortDescription: string,
     *     VenueName: ?string,
     *     EventTypeName: string
     * }>
     */
    public function findAllByType(int $eventTypeId): array;

    /**
     * Find all events with type and venue info.
     *
     * @return array<int, array{
     *     EventId: int,
     *     EventTypeId: int,
     *     Title: string,
     *     ShortDescription: string,
     *     VenueName: ?string,
     *     EventTypeName: string,
     *     EventTypeSlug: string,
     *     SessionCount: int,
     *     IsActive: bool
     * }>
     */
    public function findAllWithDetails(): array;

    /**
     * Find an event by ID.
     *
     * @param int $eventId
     * @return Event|null
     */
    public function findById(int $eventId): ?Event;

    /**
     * Find an event by ID with related details.
     *
     * @param int $eventId
     * @return array{
     *     EventId: int,
     *     EventTypeId: int,
     *     Title: string,
     *     ShortDescription: string,
     *     LongDescriptionHtml: string,
     *     VenueId: ?int,
     *     VenueName: ?string,
     *     EventTypeName: string,
     *     EventTypeSlug: string,
     *     IsActive: bool
     * }|null
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

