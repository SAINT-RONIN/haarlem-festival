<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Event;
use App\Models\EventFilter;
use App\Models\EventWithDetails;
use App\Models\JazzArtistDetailEvent;
use App\Models\RestaurantDetailEvent;
use App\Models\StorytellingDetailEvent;

interface IEventRepository
{
    /**
     * @return EventWithDetails[]
     */
    public function findEvents(EventFilter $filters = new EventFilter()): array;

    public function findActiveJazzBySlug(string $slug): ?JazzArtistDetailEvent;

    public function findActiveStorytellingBySlug(string $slug): ?StorytellingDetailEvent;

    public function findActiveRestaurantBySlug(string $slug): ?RestaurantDetailEvent;

    /**
     * @return RestaurantDetailEvent[]
     */
    public function findActiveRestaurantEvents(): array;

    public function findById(int $eventId): ?Event;

    public function create(array $data): int;

    public function update(int $eventId, array $data): bool;

    public function delete(int $eventId): bool;

    public function exists(int $eventId): bool;

    public function softDelete(int $eventId): bool;

    public function deactivateSessions(int $eventId): bool;
}
