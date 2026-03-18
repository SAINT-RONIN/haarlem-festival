<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries the event, CMS content, and pre-resolved fields needed to render a single Storytelling detail page.
 * The reason for this is because the service resolves fallback logic (about body, image path, labels) before packing everything here so the mapper can do pure formatting with no decisions.
 */
final readonly class StorytellingDetailPageData
{
    /**
     * @param StorytellingDetailEvent $event The storytelling event
     * @param array<string, mixed> $cms CMS content for this event
     * @param ?string $featuredImagePath Resolved file path for the featured image
     * @param string[] $labels Session label texts (e.g. "English", "Beginner")
     * @param string $aboutBody Resolved about section body text
     * @param array<string, mixed> $globalUiContent CMS content for the global_ui section
     */
    public function __construct(
        public StorytellingDetailEvent $event,
        public array $cms,
        public ?string $featuredImagePath,
        public array $labels,
        public string $aboutBody,
        public array $globalUiContent,
    ) {
    }
}
