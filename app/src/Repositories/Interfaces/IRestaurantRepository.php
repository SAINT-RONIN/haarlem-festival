<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

/**
 * Interface for Restaurant repository.
 */
interface IRestaurantRepository
{
    /**
     * Returns all active restaurants.
     *
     * @return array Array of Restaurant data
     */
    public function findAllActive(): array;
}

