<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

/**
 * Carries display data for a single image in the masonry grid (URL, alt text, size class).
 * The reason for this is because each image needs three typed fields and the sizeClass drives CSS layout, so a plain string array would lose that typing.
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
