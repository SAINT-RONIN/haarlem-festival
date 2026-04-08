<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

/**
 * Carries display-ready data for the "About" section on a Storytelling detail page.
 * The reason for this is because the about section has a distinct layout with a heading, HTML body, and two images, so it gets its own ViewModel rather than being packed into the parent ViewModel as loose fields.
 */
final readonly class StorytellingAboutSectionData
{
    public function __construct(
        public string $heading,
        public string $bodyHtml,
        public string $image1Url,
        public string $image2Url,
    ) {}
}
