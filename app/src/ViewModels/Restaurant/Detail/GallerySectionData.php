<?php

declare(strict_types=1);

namespace App\ViewModels\Restaurant\Detail;

/**
 * ViewModel for the Gallery section on the restaurant detail page.
 */
final readonly class GallerySectionData
{
    /**
     * @param string[] $images Gallery image paths
     */
    public function __construct(
        public array  $images,

        // CMS label
        public string $labelTitle = 'Restaurant Gallery',
    ) {
    }
}