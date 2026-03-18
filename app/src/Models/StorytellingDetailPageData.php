<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Domain payload for a Storytelling detail page.
 */
final readonly class StorytellingDetailPageData
{
    /**
     * @param StorytellingDetailEvent $event The storytelling event
     * @param array<string, mixed> $cms CMS content for this event
     * @param ?string $featuredImagePath Resolved file path for the featured image
     * @param string[] $labels Session label texts (e.g. "English", "Beginner")
     * @param string $aboutBody Resolved about section body text
     */
    public function __construct(
        public StorytellingDetailEvent $event,
        public array $cms,
        public ?string $featuredImagePath,
        public array $labels,
        public string $aboutBody,
    ) {
    }
}
