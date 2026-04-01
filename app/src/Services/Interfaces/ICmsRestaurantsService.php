<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\RestaurantDetailEvent;

interface ICmsRestaurantsService
{
    /** @return RestaurantDetailEvent[] */
    public function getRestaurants(?string $search): array;

    public function findById(int $id): ?RestaurantDetailEvent;

    /** @return array<string, string> */
    public function validateForCreate(array $data): array;

    /** @return array<string, string> */
    public function validateForUpdate(int $id, array $data): array;

    public function createRestaurant(array $data): int;

    public function updateRestaurant(int $id, array $data): void;

    public function deleteRestaurant(int $id): void;
}
