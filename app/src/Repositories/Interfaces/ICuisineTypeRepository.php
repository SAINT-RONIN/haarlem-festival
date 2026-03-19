<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\CuisineType;

interface ICuisineTypeRepository
{
    /**
     * Returns all cuisine types for a restaurant, ordered by name.
     *
     * @return CuisineType[]
     */
    public function findByRestaurantId(int $restaurantId): array;

    /**
     * @param int[] $restaurantIds
     * @return array<int, CuisineType[]> Keyed by RestaurantId
     */
    public function findByRestaurantIds(array $restaurantIds): array;
}
