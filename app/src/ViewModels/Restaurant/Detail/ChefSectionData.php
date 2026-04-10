<?php

declare(strict_types=1);

namespace App\ViewModels\Restaurant\Detail;

/**
 * ViewModel for the "Chef & Philosophy" section on the restaurant detail page.
 */
final readonly class ChefSectionData
{
    public function __construct(
        public string $name,
        public string $text,
        public string $image,

        // CMS label
        public string $labelTitle = 'Chef & Philosophy',
    ) {}
}
