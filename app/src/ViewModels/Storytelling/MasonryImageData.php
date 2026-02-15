<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

/**
 * DTO for a single masonry grid image.
 */
final readonly class MasonryImageData
{
    public function __construct(
        public string $imageUrl,
        public string $altText,
        public string $sizeClass,
    ) {
    }
}
