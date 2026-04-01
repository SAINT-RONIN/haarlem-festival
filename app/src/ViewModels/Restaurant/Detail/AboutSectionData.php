<?php

declare(strict_types=1);

namespace App\ViewModels\Restaurant\Detail;

/**
 * ViewModel for the "About" section on the restaurant detail page.
 *
 * Contains the restaurant description text and image.
 * Note: aboutText may contain safe HTML (<strong>) from the CMS.
 */
final readonly class AboutSectionData
{
    public function __construct(
        public string $text,
        public string $image,

        // CMS label
        public string $labelTitlePrefix = 'About',
    ) {
    }
}
