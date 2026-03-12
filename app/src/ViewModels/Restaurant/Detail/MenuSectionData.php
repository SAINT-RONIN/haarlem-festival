<?php

declare(strict_types=1);

namespace App\ViewModels\Restaurant\Detail;

/**
 * ViewModel for the "Menu Style" section on the restaurant detail page.
 */
final readonly class MenuSectionData
{
    /**
     * @param string[] $cuisineTags  Individual cuisine type tags (e.g. ["French", "European"])
     */
    public function __construct(
        public string $description,
        public array  $cuisineTags,
        public string $image1,
        public string $image2,

        // CMS labels
        public string $labelTitle       = 'Menu Style',
        public string $labelCuisineType = 'Cuisine type:',
    ) {
    }
}
