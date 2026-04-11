<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * Split-layout intro section data shared across landing pages.
 */
final readonly class IntroSplitSectionData
{
    public function __construct(
        public string $headingText,
        public string $bodyText,
        public string $imageUrl,
        public string $imageAltText,
        public ?array $subsections = null,
        public ?string $closingLine = null,
        public ?string $label = null,
    ) {}
}
