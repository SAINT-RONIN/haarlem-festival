<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

/**
 * Interface for Restaurant page service.
 *
 * The service returns plain arrays with business data.
 * The mapper (RestaurantViewModelMapper) converts this data into ViewModels.
 */
interface IRestaurantService
{
    /**
     * Returns all data needed by the restaurant listing page.
     *
     * @return array{
     *     gradientCms: array,
     *     introCms: array,
     *     intro2Cms: array,
     *     instructionsCms: array,
     *     cardsCms: array,
     *     restaurants: \App\Models\Restaurant[],
     *     cuisineFilters: string[],
     *     cards: array
     * }
     */
    public function getRestaurantPageData(): array;

    /**
     * Returns all data needed by a single restaurant detail page.
     * Returns null if the restaurant is not found.
     *
     * @return array|null
     */
    public function getRestaurantDetailData(int $id): ?array;
}
