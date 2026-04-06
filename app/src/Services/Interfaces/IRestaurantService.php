<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Domain\Pages\RestaurantPageData;

/**
 * Interface for Restaurant page service.
 */
interface IRestaurantService
{
    public function getRestaurantPageData(): RestaurantPageData;
}
