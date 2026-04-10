<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

/**
 * Carries display data for a single story highlight card (image, title, description).
 * The reason for this is because each highlight is a repeating item with its own typed fields, so it gets its own class instead of being an untyped sub-array inside StoryHighlightsSectionData.
 */
final readonly class StoryHighlightData
{
    public function __construct(
        public string $imageUrl,
        public string $title,
        public string $description,
    ) {}
}
