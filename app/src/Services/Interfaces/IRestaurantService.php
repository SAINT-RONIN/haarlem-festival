<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\RestaurantDetailData;
use App\Models\RestaurantPageData;

/**
 * Interface for Restaurant page service.
 */
interface IRestaurantService
{
    public function getRestaurantPageData(): RestaurantPageData;

    public function getRestaurantDetailData(int $id): ?RestaurantDetailData;
}
