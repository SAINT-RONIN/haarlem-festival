<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Models\RestaurantDetailEvent;
use App\ViewModels\Cms\CmsRestaurantFormViewModel;
use App\ViewModels\Cms\CmsRestaurantListItemViewModel;
use App\ViewModels\Cms\CmsRestaurantsListViewModel;

class CmsRestaurantsMapper
{
    /**
     * @param RestaurantDetailEvent[] $restaurants
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
        ?int $eventId,
        array $data,
        string $csrfToken,
        string $formAction,
        string $pageTitle,
        array $errors,
    ): CmsRestaurantFormViewModel {
        return new CmsRestaurantFormViewModel(
            eventId:              $eventId,
            title:                $data['Title'] ?? '',
            slug:                 $data['Slug'] ?? '',
            shortDescription:     $data['ShortDescription'] ?? '',
            longDescriptionHtml:  $data['LongDescriptionHtml'] ?? '',
            featuredImageAssetId: isset($data['FeaturedImageAssetId']) ? (int)$data['FeaturedImageAssetId'] : null,
            isActive:             (bool)($data['IsActive'] ?? true),
            csrfToken:            $csrfToken,
            formAction:           $formAction,
            pageTitle:            $pageTitle,
            errors:               $errors,
        );
    }

    public static function fromEvent(RestaurantDetailEvent $e): array
    {
        return [
            'Title'               => $e->title,
            'Slug'                => $e->slug,
            'ShortDescription'    => $e->shortDescription,
            'LongDescriptionHtml' => $e->longDescriptionHtml,
            'FeaturedImageAssetId' => $e->featuredImageAssetId,
            'IsActive'            => true,
        ];
    }

    private static function toListItemViewModel(RestaurantDetailEvent $e): CmsRestaurantListItemViewModel
    {
        return new CmsRestaurantListItemViewModel(
            eventId:  $e->eventId,
            title:    $e->title,
            slug:     $e->slug,
            isActive: true,
        );
    }
}
