<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Domain\Pages\RestaurantPageData;
use App\DTOs\Domain\Restaurant\ReservationFormData;
use App\DTOs\Domain\Restaurant\RestaurantDetailPageData;
interface IRestaurantService
{
    public function getRestaurantPageData(): RestaurantPageData;

    /** @throws \App\Exceptions\RestaurantEventNotFoundException */
    public function getDetailPageData(string $slug): RestaurantDetailPageData;

    /**
     * @throws \App\Exceptions\RestaurantEventNotFoundException
     * @throws \App\Exceptions\ValidationException
     * @return int The new reservation ID
     */
    public function submitReservation(string $slug, ReservationFormData $formData): int;
}