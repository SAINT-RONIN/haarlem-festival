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
     * @param string $scheduleCtaButtonText CTA button text for the schedule section
     * @param EventHighlight[] $highlights Highlight cards from the event_highlights table
     * @param EventGalleryImage[] $galleryImages Gallery images (imageType='gallery') from event_gallery_images
     * @param EventGalleryImage[] $aboutImages About section images (imageType='about') from event_gallery_images
     */
    public function __construct(
        public StorytellingDetailEvent $event,
        public array $cms,
        public ?string $featuredImagePath,
        public array $labels,
        public string $aboutBody,
        public array $globalUiContent,
        public string $scheduleCtaButtonText = '',
        public array $highlights = [],
        public array $galleryImages = [],
        public array $aboutImages = [],
    ) {
    }
}
