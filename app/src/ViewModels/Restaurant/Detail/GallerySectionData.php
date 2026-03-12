<?php

declare(strict_types=1);

namespace App\ViewModels\Restaurant\Detail;

/**
 * ViewModel for the Gallery section on the restaurant detail page.
 */
final readonly class GallerySectionData
{
    public function __construct(
        public string $image1,
        public string $image2,
        public string $image3,

        // CMS label
        public string $labelTitle = 'Restaurant Gallery',
    ) {
    }
}
