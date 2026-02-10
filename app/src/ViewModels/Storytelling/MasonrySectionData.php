<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

/**
 * DTO for the masonry grid section.
 */
final readonly class MasonrySectionData
{
    /**
     * @param array<int, MasonryImageData[]> $columns
     */
    public function __construct(
        public string $headingText,
        public array $columns,
    ) {
    }
}

