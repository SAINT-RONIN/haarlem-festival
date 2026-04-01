<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\RestaurantPageData;

/**
 * Builds the Restaurant overview page domain payload.
 */
interface IRestaurantService
{
    public function getRestaurantPageData(): RestaurantPageData;
}
