<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\ViewModels\Restaurant\RestaurantPageViewModel;

/**
 * Interface for Restaurant page service.
 */
interface IRestaurantService
{
    /**
     * Builds the restaurant page view model with all required data.
     */
    public function getRestaurantPageData(): RestaurantPageViewModel;
}
