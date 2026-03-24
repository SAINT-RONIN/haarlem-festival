<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Restaurant;
use App\Models\RestaurantUpsertData;
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

    public function createRestaurant(RestaurantUpsertData $data): int
    {
        return $this->restaurantRepository->create($data);
    }

    public function updateRestaurant(int $id, RestaurantUpsertData $data): void
    {
        $this->restaurantRepository->update($id, $data);
    }

    public function deleteRestaurant(int $id): void
    {
        $this->restaurantRepository->delete($id);
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
