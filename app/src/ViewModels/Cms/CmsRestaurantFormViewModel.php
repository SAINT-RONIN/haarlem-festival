<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

final readonly class CmsRestaurantFormViewModel
{
    /**
     * @param array<string, string> $errors
     */
    public function __construct(
        public ?int    $eventId,
        public string  $title,
        public string  $slug,
        public string  $shortDescription,
        public string  $longDescriptionHtml,
        public ?int    $featuredImageAssetId,
        public bool    $isActive,
        public string  $csrfToken,
        public string  $formAction,
        public string  $pageTitle,
        public array   $errors,
    ) {}
}
