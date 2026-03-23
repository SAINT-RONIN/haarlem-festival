<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * Gradient banner section data shared across Jazz and Storytelling pages.
 */
final readonly class GradientSectionData
{
    public function __construct(
        public string $headingText,
        public string $subheadingText,
        public string $backgroundImageUrl,
    ) {
    }
}
