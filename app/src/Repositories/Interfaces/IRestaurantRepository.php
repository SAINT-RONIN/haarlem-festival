<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\Restaurant;

/**
 * Interface for Restaurant repository.
 */
interface IRestaurantRepository
{
    /**
     * Returns all active restaurants.
     *
     * @return Restaurant[]
     */
    public function findAllActive(): array;

    /**
     * Returns a single restaurant by ID, or null if not found.
     */
    public function findById(int $id): ?Restaurant;

    /**
     * Returns all restaurants (including inactive), optionally filtered by name.
     *
     * @return Restaurant[]
     */
    public function findAll(?string $search = null): array;

    public function delete(int $id): void;
}
