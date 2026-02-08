<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * DTO for a single masonry grid image.
 *
 * All fields are guaranteed to be populated by the Service layer.
 */
final readonly class MasonryImageData
{
    public function __construct(
        public string $imageUrl,
        public string $altText,
        public string $sizeClass,
    )
    {
    }
}

