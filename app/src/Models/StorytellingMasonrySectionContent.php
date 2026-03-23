<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries CMS item values for the Storytelling masonry_section.
 * Image paths are stored in order as a typed array instead of 12 individual properties.
 */
final readonly class StorytellingMasonrySectionContent
{
    private const IMAGE_COUNT = 12;

    /**
     * @param string[] $imagePaths Up to 12 image paths in order (may contain empty strings for missing images)
     */
    public function __construct(
        public ?string $masonryHeading,
        public array $imagePaths,
    ) {}

    /**
     * @param array<string, ?string> $raw CMS item values keyed by item key
     */
    public static function fromRawArray(array $raw): self
    {
        $paths = [];
        for ($i = 1; $i <= self::IMAGE_COUNT; $i++) {
            $paths[] = $raw[sprintf('masonry_image_%02d', $i)] ?? '';
        }

        return new self(
            masonryHeading: $raw['masonry_heading'] ?? null,
            imagePaths: $paths,
        );
    }
}
