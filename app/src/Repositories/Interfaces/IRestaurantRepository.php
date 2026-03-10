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
}
