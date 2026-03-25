<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Restaurant;
use App\DTOs\Cms\RestaurantUpsertData;
use App\Exceptions\CmsOperationException;
use App\Repositories\Interfaces\IRestaurantRepository;
use App\Services\Interfaces\ICmsRestaurantsService;

class CmsRestaurantsService implements ICmsRestaurantsService
{
    public function __construct(
        private readonly IRestaurantRepository $restaurantRepository,
    ) {}

    /** @return Restaurant[] */
    public function getRestaurants(?string $search): array
    {
        return $this->restaurantRepository->findAll($search);
    }

    public function findById(int $id): ?Restaurant
    {
        return $this->restaurantRepository->findById($id);
    }

    /** @return array<string, string> */
    public function validateForCreate(RestaurantUpsertData $data): array
    {
        return $this->validate($data);
    }

    /** @return array<string, string> */
    public function validateForUpdate(int $id, RestaurantUpsertData $data): array
    {
        return $this->validate($data);
    }

    /** @throws CmsOperationException When the database write fails */
    public function createRestaurant(RestaurantUpsertData $data): int
    {
        try {
            return $this->restaurantRepository->create($data);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to create restaurant.', 0, $error);
        }
    }

    /** @throws CmsOperationException When the database write fails */
    public function updateRestaurant(int $id, RestaurantUpsertData $data): void
    {
        try {
            $this->restaurantRepository->update($id, $data);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to update restaurant.', 0, $error);
        }
    }

    /** @throws CmsOperationException When the database write fails */
    public function deleteRestaurant(int $id): void
    {
        try {
            $this->restaurantRepository->delete($id);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to delete restaurant.', 0, $error);
        }
    }

    /** @return array<string, string> */
    private function validate(RestaurantUpsertData $data): array
    {
        $errors = [];
        if ($data->name === '') {
            $errors['name'] = 'Name is required.';
        }
        if ($data->addressLine === '') {
            $errors['addressLine'] = 'Address is required.';
        }
        if ($data->city === '') {
            $errors['city'] = 'City is required.';
        }
        if ($data->cuisineType === '') {
            $errors['cuisineType'] = 'Cuisine type is required.';
        }
        if ($data->descriptionHtml === '') {
            $errors['descriptionHtml'] = 'Description is required.';
        }
        return $errors;
    }
}
