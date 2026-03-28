<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Models\Restaurant;
use App\Models\RestaurantUpsertData;
use App\ViewModels\Cms\CmsRestaurantFormViewModel;
use App\ViewModels\Cms\CmsRestaurantListItemViewModel;
use App\ViewModels\Cms\CmsRestaurantsListViewModel;

class CmsRestaurantsMapper
{
    /**
     * @param Restaurant[] $restaurants
     */
    public static function toListViewModel(
        array $restaurants,
        string $searchQuery,
        ?string $successMessage,
        ?string $errorMessage,
        string $deleteCsrfToken,
    ): CmsRestaurantsListViewModel {
        return new CmsRestaurantsListViewModel(
            items: array_map([self::class, 'toListItemViewModel'], $restaurants),
            searchQuery: $searchQuery,
            successMessage: $successMessage,
            errorMessage: $errorMessage,
            deleteCsrfToken: $deleteCsrfToken,
        );
    }

    /**
     * @param array<string, string> $errors
     */
    public static function toFormViewModel(
        ?int $restaurantId,
        RestaurantUpsertData $data,
        string $csrfToken,
        string $formAction,
        string $pageTitle,
        array $errors,
    ): CmsRestaurantFormViewModel {
        return new CmsRestaurantFormViewModel(
            restaurantId:    $restaurantId,
            name:            $data->name,
            addressLine:     $data->addressLine,
            city:            $data->city,
            stars:           $data->stars,
            cuisineType:     $data->cuisineType,
            descriptionHtml: $data->descriptionHtml,
            imageAssetId:    $data->imageAssetId,
            isActive:        $data->isActive,
            csrfToken:       $csrfToken,
            formAction:      $formAction,
            pageTitle:       $pageTitle,
            errors:          $errors,
        );
    }

    public static function fromRestaurant(Restaurant $r): RestaurantUpsertData
    {
        return new RestaurantUpsertData(
            name:            $r->name,
            addressLine:     $r->addressLine,
            city:            $r->city,
            stars:           $r->stars,
            cuisineType:     $r->cuisineType,
            descriptionHtml: $r->descriptionHtml,
            imageAssetId:    $r->imageAssetId,
            isActive:        $r->isActive,
        );
    }

    private static function toListItemViewModel(Restaurant $r): CmsRestaurantListItemViewModel
    {
        return new CmsRestaurantListItemViewModel(
            restaurantId: $r->restaurantId,
            name:         $r->name,
            cuisineType:  $r->cuisineType,
            city:         $r->city,
            isActive:     $r->isActive,
            createdAt:    $r->createdAtUtc->format('Y-m-d'),
        );
    }
}