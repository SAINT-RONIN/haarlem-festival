<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

/**
 * Carries the heading and typed collection of StoryHighlightData items for the highlights section.
 * The reason for this is because wrapping the heading and items together gives the view one predictable object for the entire section rather than two separate variables.
 */
final readonly class StoryHighlightsSectionData
{
    /**
     * @param StoryHighlightData[] $items
     */
    public function __construct(
        public string $heading,
        public array $items,
    ) {}
}
