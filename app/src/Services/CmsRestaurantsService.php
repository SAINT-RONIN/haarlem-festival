<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Restaurant;
use App\DTOs\Cms\RestaurantUpsertData;
use App\Exceptions\CmsOperationException;
use App\Helpers\FieldValidator;
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
        FieldValidator::requireNonEmpty('name', $data->name, 'Name', $errors);
        FieldValidator::requireNonEmpty('addressLine', $data->addressLine, 'Address', $errors);
        FieldValidator::requireNonEmpty('city', $data->city, 'City', $errors);
        FieldValidator::requireNonEmpty('cuisineType', $data->cuisineType, 'Cuisine type', $errors);
        FieldValidator::requireNonEmpty('descriptionHtml', $data->descriptionHtml, 'Description', $errors);
        return $errors;
    }
}
