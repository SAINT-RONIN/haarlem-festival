<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Event;
use App\Models\EventFilter;
use App\Models\EventWithDetails;
use App\Models\JazzArtistDetailEvent;
use App\Models\StorytellingDetailEvent;

/**
 * Defines persistence operations for festival events.
 */
interface IEventRepository
{
    /**
     * Queries events with joined venue and type details using optional filters.
     *
     * @return EventWithDetails[]
     */
    public function findEvents(EventFilter $filters = new EventFilter()): array;

    /**
     * Finds an active jazz event by its URL slug, including artist-specific detail fields.
     */
    public function findActiveJazzBySlug(string $slug): ?JazzArtistDetailEvent;

    /**
     * Finds an active storytelling event by its URL slug, including storytelling-specific detail fields.
     */
    public function findActiveStorytellingBySlug(string $slug): ?StorytellingDetailEvent;

    /**
     * Finds a single event by its primary key.
     */
    public function findById(int $eventId): ?Event;

    /**
     * Inserts a new event and returns the generated ID.
     */
    public function create(array $data): int;

    /**
     * Updates an event's columns and returns whether any row was affected.
     */
    public function update(int $eventId, array $data): bool;

    /**
     * Hard-deletes an event by its ID.
     */
    public function delete(int $eventId): bool;

    /**
     * Checks whether an event with the given ID exists.
     */
    public function exists(int $eventId): bool;

    /**
     * Marks an event as deleted without removing the row (sets IsActive to false).
     */
    public function softDelete(int $eventId): bool;

    /**
     * Deactivates all sessions belonging to the given event.
     */
    public function deactivateSessions(int $eventId): bool;
}
