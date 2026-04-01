<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Restaurant;
use App\DTOs\Cms\RestaurantUpsertData;
use App\Exceptions\CmsOperationException;
use App\Helpers\FieldValidator;
use App\Repositories\Interfaces\IRestaurantRepository;
use App\Services\Interfaces\ICmsRestaurantsService;

/**
 * Contains the CMS business rules for searching, validating, and saving restaurants.
 */
class CmsRestaurantsService implements ICmsRestaurantsService
{
    /** Stores the restaurant repository used by the CMS restaurant management screens. */
    public function __construct(
        private readonly IRestaurantRepository $restaurantRepository,
    ) {}

    /** Returns the restaurants shown in the CMS list page, optionally filtered by search text. */
    /** @return Restaurant[] */
    public function getRestaurants(?string $search): array
    {
        return $this->restaurantRepository->findAll($search);
    }

    /** Loads one restaurant for the CMS edit page. */
    public function findById(int $id): ?Restaurant
    {
        return $this->restaurantRepository->findById($id);
    }

    /** Validates data for the create form before a new restaurant is inserted. */
    /** @return array<string, string> */
    public function validateForCreate(RestaurantUpsertData $data): array
    {
        return $this->validate($data);
    }

    /** Validates data for the edit form before an existing restaurant is updated. */
    /** @return array<string, string> */
    public function validateForUpdate(int $id, RestaurantUpsertData $data): array
    {
        return $this->validate($data);
    }

    /** Creates a new restaurant record and wraps repository failures in a CMS-specific exception. */
    /** @throws CmsOperationException When the database write fails */
    public function createRestaurant(RestaurantUpsertData $data): int
    {
        try {
            return $this->restaurantRepository->create($data);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to create restaurant.', 0, $error);
        }
    }

    /** Updates an existing restaurant record and wraps repository failures in a CMS-specific exception. */
    /** @throws CmsOperationException When the database write fails */
    public function updateRestaurant(int $id, RestaurantUpsertData $data): void
    {
        try {
            $this->restaurantRepository->update($id, $data);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to update restaurant.', 0, $error);
        }
    }

    /** Soft-deletes a restaurant record and wraps repository failures in a CMS-specific exception. */
    /** @throws CmsOperationException When the database write fails */
    public function deleteRestaurant(int $id): void
    {
        try {
            $this->restaurantRepository->delete($id);
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to delete restaurant.', 0, $error);
        }
    }

    /** Applies the shared required-field validation rules used by both create and update. */
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
