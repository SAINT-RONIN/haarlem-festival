<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * DTO for the masonry grid section.
 *
 * All fields are guaranteed to be populated by the Service layer.
 * Images are pre-organized into columns for aligned top/bottom edges.
 */
final readonly class MasonrySectionData
{
    public function __construct(
        public string $headingText,
        /** @var array<int, MasonryImageData[]> Column index => array of images */
        public array  $columns,
    )
    {
    }
}

