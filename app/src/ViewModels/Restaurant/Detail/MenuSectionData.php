<?php

declare(strict_types=1);

namespace App\ViewModels\Restaurant\Detail;

/**
 * ViewModel for the "Menu Style" section on the restaurant detail page.
 */
final readonly class MenuSectionData
{
    /**
     * @param string[] $cuisineTags Individual cuisine type tags (e.g. ["French", "European"])
     * @param string[] $images      Menu dish image paths
     */
    public function __construct(
        public string $description,
        public array  $cuisineTags,
        public array  $images,

        // CMS labels
        public string $labelTitle       = 'Menu Style',
        public string $labelCuisineType = 'Cuisine type:',
    ) {
    }
}