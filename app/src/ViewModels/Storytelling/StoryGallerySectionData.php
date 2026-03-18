<?php

declare(strict_types=1);

namespace App\ViewModels\Storytelling;

/**
 * Carries the heading and two named image rows for the gallery section on the detail page.
 * The reason for this is because the view renders two visually distinct rows, so splitting images into topRowImages and bottomRowImages at mapping time keeps that layout decision out of the template.
 */
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
