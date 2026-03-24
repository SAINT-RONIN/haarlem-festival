<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\RestaurantImage;

interface IRestaurantImageRepository
{
    /**
     * Returns all images for a restaurant, ordered by ImageType then SortOrder.
     *
     * @return RestaurantImage[]
     */
    public function findByRestaurantId(int $restaurantId): array;
}
