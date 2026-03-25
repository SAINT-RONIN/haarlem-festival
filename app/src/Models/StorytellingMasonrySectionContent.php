<?php

declare(strict_types=1);

namespace App\Models;

/**
 * CMS content for the storytelling page masonry image grid section.
 * Hydrated from CMS key-value pairs.
 */
final readonly class StorytellingMasonrySectionContent
{
    public const IMAGE_COUNT = 12;

    /**
     * @param string[] $imagePaths Up to 12 image paths in order (may contain empty strings for missing images)
     */
    public function __construct(
        public ?string $masonryHeading,
        public array $imagePaths,
    ) {}
}
