<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

/**
 * Carries display data for the video section on the detail page (heading, URL, placeholder text).
 * The reason for this is because even though the section is small, a typed object prevents the view from receiving a loose array and accidentally accessing a missing key.
 */
final readonly class StoryVideoSectionData
{
    public function __construct(
        public string $heading,
        public string $url,
        public string $placeholderText,
    ) {
    }
}
