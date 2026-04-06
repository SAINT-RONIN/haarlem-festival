<?php

declare(strict_types=1);

namespace App\DTOs\Cms;

/**
 * CMS content for a page's gradient banner section.
 * Shared across Jazz, Storytelling, and other pages that use the same gradient layout.
 * Hydrated from CMS key-value pairs.
 */
final readonly class GradientSectionContent
{
    public function __construct(
        public ?string $gradientHeading,
        public ?string $gradientSubheading,
        public ?string $gradientBackgroundImage,
    ) {
    }
}
