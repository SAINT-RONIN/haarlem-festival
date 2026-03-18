<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

final readonly class StoryHighlightsSectionData
{
    /**
     * @param StoryHighlightData[] $items
     */
    public function __construct(
        public string $heading,
        public array $items,
    ) {
    }
}
