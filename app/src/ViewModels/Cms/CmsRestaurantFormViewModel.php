<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

final readonly class CmsRestaurantFormViewModel
{
    /**
     * @param array<string, string> $errors
     */
    public function __construct(
        public ?int    $restaurantId,
        public string  $name,
        public string  $addressLine,
        public string  $city,
        public ?int    $stars,
        public string  $cuisineType,
        public string  $descriptionHtml,
        public ?int    $imageAssetId,
        public bool    $isActive,
        public ?string $phone,
        public ?string $email,
        public ?string $website,
        public ?string $aboutText,
        public ?string $chefName,
        public ?string $chefText,
        public ?string $menuDescription,
        public ?string $locationDescription,
        public ?string $mapEmbedUrl,
        public ?int    $michelinStars,
        public ?int    $seatsPerSession,
        public ?int    $durationMinutes,
        public ?string $specialRequestsNote,
        public string  $csrfToken,
        public string  $formAction,
        public string  $pageTitle,
        public array   $errors,
    ) {}
}
