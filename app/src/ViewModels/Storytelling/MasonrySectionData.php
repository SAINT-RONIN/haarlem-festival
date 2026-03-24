<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

/**
 * Carries the heading and typed image collection for the masonry grid section.
 * The reason for this is because grouping the heading with its images as one typed object keeps the mapper and view aligned on what the section contains.
 */
final readonly class MasonrySectionData
{
    /**
     * @param MasonryImageData[] $images
     */
    public function __construct(
        public string $headingText,
        public array $images,
    ) {
    }
}
