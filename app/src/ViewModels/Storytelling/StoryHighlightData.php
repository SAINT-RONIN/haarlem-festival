<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

final readonly class StoryHighlightData
{
    public function __construct(
        public string $imageUrl,
        public string $title,
        public string $description,
    ) {
    }
}
