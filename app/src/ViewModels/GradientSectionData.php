<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * DTO for the gradient text section.
 *
 * All fields are guaranteed to be populated by the Service layer.
 */
final readonly class GradientSectionData
{
    public function __construct(
        public string $headingText,
        public string $subheadingText,
        public string $backgroundImageUrl,
    )
    {
    }
}

