<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

final readonly class StoryGallerySectionData
{
    /**
     * @param string[] $topRowImages
     * @param string[] $bottomRowImages
     */
    public function __construct(
        public string $heading,
        public array $topRowImages,
        public array $bottomRowImages,
    ) {
    }
}
