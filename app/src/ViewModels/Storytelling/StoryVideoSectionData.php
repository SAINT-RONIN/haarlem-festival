<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

final readonly class StoryVideoSectionData
{
    public function __construct(
        public string $heading,
        public string $url,
        public string $placeholderText,
    ) {
    }
}
