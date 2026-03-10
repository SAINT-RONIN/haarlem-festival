<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\ViewModels\Restaurant\RestaurantDetailViewModel;
use App\ViewModels\Restaurant\RestaurantPageViewModel;

/**
 * Interface for Restaurant page service.
 */
interface IRestaurantService
{
    /**
     * Builds the restaurant listing page view model.
     */
    public function getRestaurantPageData(): RestaurantPageViewModel;

    /**
     * Builds the restaurant detail page view model.
     * Returns null if the restaurant is not found.
     */
    public function getRestaurantDetailData(int $id): ?RestaurantDetailViewModel;
}
