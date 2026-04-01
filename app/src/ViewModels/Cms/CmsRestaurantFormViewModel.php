<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * ViewModel for the CMS restaurant create and edit form.
 */
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
        public string  $csrfToken,
        public string  $formAction,
        public string  $pageTitle,
        public array   $errors,
    ) {}
}
