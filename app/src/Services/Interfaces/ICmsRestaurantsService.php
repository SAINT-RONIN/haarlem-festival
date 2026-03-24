<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\Restaurant;
use App\Models\RestaurantUpsertData;

interface ICmsRestaurantsService
{
    /** @return Restaurant[] */
    public function getRestaurants(?string $search): array;

    public function findById(int $id): ?Restaurant;

    /** @return array<string, string> */
    public function validateForCreate(RestaurantUpsertData $data): array;

    /** @return array<string, string> */
    public function validateForUpdate(int $id, RestaurantUpsertData $data): array;

    public function createRestaurant(RestaurantUpsertData $data): int;

    public function updateRestaurant(int $id, RestaurantUpsertData $data): void;

    public function deleteRestaurant(int $id): void;
}
