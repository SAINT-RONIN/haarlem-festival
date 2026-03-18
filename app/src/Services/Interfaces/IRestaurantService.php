<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

/**
 * Interface for Restaurant page service.
 */
interface IRestaurantService
{
    /**
     * Returns raw data arrays needed to build the restaurant listing page.
     */
    public function getRestaurantPageData(): array;

    /**
     * Returns raw data arrays needed to build the restaurant detail page.
     * Returns null if the restaurant is not found.
     */
    public function getRestaurantDetailData(int $id): ?array;
}
