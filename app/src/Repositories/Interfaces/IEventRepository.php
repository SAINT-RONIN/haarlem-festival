<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Event;
use App\DTOs\Cms\EventUpsertData;
use App\DTOs\Domain\Filters\EventFilter;
use App\DTOs\Domain\Events\EventWithDetails;
use App\DTOs\Domain\Events\JazzArtistCardRecord;
use App\DTOs\Domain\Events\JazzArtistDetailEvent;
use App\DTOs\Domain\Events\RestaurantDetailEvent;
use App\DTOs\Domain\Events\StorytellingDetailEvent;

/**
 * Contract for CRUD operations on the Event table. Supports filtered listing with
 * joined Venue/EventType data, slug-based lookups for public Jazz and Storytelling
 * detail pages, and soft-delete for deactivation without losing historical data.
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
     * Returns the artists currently visible in the Jazz overview lineup section.
     *
     * @return JazzArtistCardRecord[]
     */
    public function findJazzOverviewArtists(): array;

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
    public function create(EventUpsertData $data): int;

    /**
     * Updates an event's mutable fields.
     */
    public function update(int $eventId, EventUpsertData $data): bool;

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
     * Finds an active restaurant event by its URL slug.
     */
    public function findActiveRestaurantBySlug(string $slug): ?RestaurantDetailEvent;

    /**
     * Returns all active restaurant-type events.
     *
     * @return RestaurantDetailEvent[]
     */
    public function findActiveRestaurantEvents(): array;

    /**
     * Returns true if any event row has the given slug (optionally excluding one event ID).
     */
    public function slugExists(string $slug, ?int $excludeEventId = null): bool;
}
