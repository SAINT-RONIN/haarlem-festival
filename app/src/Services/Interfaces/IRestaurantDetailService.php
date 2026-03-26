<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\RestaurantDetailPageData;

interface IRestaurantDetailService
{
    /**
     * Assembles the full domain payload for a single restaurant event detail page.
     *
     * @throws \App\Exceptions\RestaurantEventNotFoundException if the event is not found or slug is invalid
     */
    public function getDetailPageData(string $slug): RestaurantDetailPageData;
}
