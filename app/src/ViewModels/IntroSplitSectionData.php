<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * DTO for the intro text + image split section.
 *
 * All fields are guaranteed to be populated by the Service layer.
 */
final readonly class IntroSplitSectionData
{
    public function __construct(
        public string $headingText,
        public string $bodyText,
        public string $imageUrl,
        public string $imageAltText,
    )
    {
    }
}