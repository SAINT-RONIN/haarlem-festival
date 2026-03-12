<?php

declare(strict_types=1);

namespace App\ViewModels\Restaurant\Detail;

/**
 * ViewModel for the "Location" section on the restaurant detail page.
 */
final readonly class LocationSectionData
{
    public function __construct(
        public string $description,
        public string $address,
        public string $mapEmbedUrl,

        // CMS labels
        public string $labelTitle        = 'Location',
        public string $labelAddress      = 'Address',
        public string $labelMapFallback  = 'Map not available',
    ) {
    }
}
