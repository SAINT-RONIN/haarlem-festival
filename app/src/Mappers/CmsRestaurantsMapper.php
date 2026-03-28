<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Models\Restaurant;
use App\DTOs\Cms\RestaurantUpsertData;
use App\ViewModels\Cms\CmsRestaurantFormViewModel;
use App\ViewModels\Cms\CmsRestaurantListItemViewModel;
use App\ViewModels\Cms\CmsRestaurantsListViewModel;

class CmsRestaurantsMapper
{
    public static function emptyData(): RestaurantUpsertData
    {
        return new RestaurantUpsertData('', '', '', null, '', '', null, true, null, null, null, null, null, null, null, null, null, null, null, null, null);
    }

    /**
     * @param array<string, mixed> $input
     */
    public static function fromFormInput(array $input): RestaurantUpsertData
    {
        return new RestaurantUpsertData(
            name: (string)($input['name'] ?? ''),
            addressLine: (string)($input['addressLine'] ?? ''),
            city: (string)($input['city'] ?? ''),
            stars: self::intOrNull($input['stars'] ?? null),
            cuisineType: (string)($input['cuisineType'] ?? ''),
            descriptionHtml: (string)($input['descriptionHtml'] ?? ''),
            imageAssetId: self::intOrNull($input['imageAssetId'] ?? null),
            isActive: (bool)($input['isActive'] ?? false),
            phone: self::stringOrNull($input['phone'] ?? null),
            email: self::stringOrNull($input['email'] ?? null),
            website: self::stringOrNull($input['website'] ?? null),
            aboutText: self::stringOrNull($input['aboutText'] ?? null),
            chefName: self::stringOrNull($input['chefName'] ?? null),
            chefText: self::stringOrNull($input['chefText'] ?? null),
            menuDescription: self::stringOrNull($input['menuDescription'] ?? null),
            locationDescription: self::stringOrNull($input['locationDescription'] ?? null),
            mapEmbedUrl: self::stringOrNull($input['mapEmbedUrl'] ?? null),
            michelinStars: self::intOrNull($input['michelinStars'] ?? null),
            seatsPerSession: self::intOrNull($input['seatsPerSession'] ?? null),
            durationMinutes: self::intOrNull($input['durationMinutes'] ?? null),
            specialRequestsNote: self::stringOrNull($input['specialRequestsNote'] ?? null),
        );
    }

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
        return new CmsRestaurantFormViewModel(...[
            'restaurantId' => $restaurantId,
            ...self::mapCoreFields($data),
            ...self::mapDetailFields($data),
            'csrfToken'   => $csrfToken,
            'formAction'  => $formAction,
            'pageTitle'   => $pageTitle,
            'errors'      => $errors,
        ]);
    }

    public static function fromRestaurant(Restaurant $r): RestaurantUpsertData
    {
        return new RestaurantUpsertData(...[
            ...self::mapCoreFieldsFromModel($r),
            ...self::mapDetailFieldsFromModel($r),
        ]);
    }

    private static function toListItemViewModel(Restaurant $r): CmsRestaurantListItemViewModel
    {
        return new CmsRestaurantListItemViewModel(
            restaurantId: $r->restaurantId,
            name: $r->name,
            cuisineType: $r->cuisineType,
            city: $r->city,
            isActive: $r->isActive,
            createdAt: $r->createdAtUtc->format('Y-m-d'),
        );
    }

    /** @return array<string, mixed> */
    private static function mapCoreFields(RestaurantUpsertData $data): array
    {
        return [
            'name'            => $data->name,
            'addressLine'     => $data->addressLine,
            'city'            => $data->city,
            'stars'           => $data->stars,
            'cuisineType'     => $data->cuisineType,
            'descriptionHtml' => $data->descriptionHtml,
            'imageAssetId'    => $data->imageAssetId,
            'isActive'        => $data->isActive,
        ];
    }

    /** @return array<string, mixed> */
    private static function mapDetailFields(RestaurantUpsertData $data): array
    {
        return [
            'phone'               => $data->phone,
            'email'               => $data->email,
            'website'             => $data->website,
            'aboutText'           => $data->aboutText,
            'chefName'            => $data->chefName,
            'chefText'            => $data->chefText,
            'menuDescription'     => $data->menuDescription,
            'locationDescription' => $data->locationDescription,
            'mapEmbedUrl'         => $data->mapEmbedUrl,
            'michelinStars'       => $data->michelinStars,
            'seatsPerSession'     => $data->seatsPerSession,
            'durationMinutes'     => $data->durationMinutes,
            'specialRequestsNote' => $data->specialRequestsNote,
        ];
    }

    /** @return array<string, mixed> */
    private static function mapCoreFieldsFromModel(Restaurant $r): array
    {
        return [
            'name'            => $r->name,
            'addressLine'     => $r->addressLine,
            'city'            => $r->city,
            'stars'           => $r->stars,
            'cuisineType'     => $r->cuisineType,
            'descriptionHtml' => $r->descriptionHtml,
            'imageAssetId'    => $r->imageAssetId,
            'isActive'        => $r->isActive,
        ];
    }

    /** @return array<string, mixed> */
    private static function mapDetailFieldsFromModel(Restaurant $r): array
    {
        return [
            'phone'               => $r->phone,
            'email'               => $r->email,
            'website'             => $r->website,
            'aboutText'           => $r->aboutText,
            'chefName'            => $r->chefName,
            'chefText'            => $r->chefText,
            'menuDescription'     => $r->menuDescription,
            'locationDescription' => $r->locationDescription,
            'mapEmbedUrl'         => $r->mapEmbedUrl,
            'michelinStars'       => $r->michelinStars,
            'seatsPerSession'     => $r->seatsPerSession,
            'durationMinutes'     => $r->durationMinutes,
            'specialRequestsNote' => $r->specialRequestsNote,
        ];
    }

    private static function stringOrNull(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $trimmed = trim($value);
        return $trimmed === '' ? null : $trimmed;
    }

    private static function intOrNull(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int)$value;
    }
}
