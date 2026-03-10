<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

/**
 * DTO for a single story highlight card on the storytelling detail page.
 */
final readonly class StoryHighlightData
{
    public function __construct(
        public string $imageUrl,
        public string $title,
        public string $description,
    ) {
    }
}
