<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Carries all CMS sections needed to render the Storytelling overview page.
 * The reason for this is because the service must return a typed object rather than a raw array so the mapper and controller always know exactly what fields are available.
 */
final readonly class StorytellingPageData
{
    /**
     * @param \App\Models\PageGalleryImage[] $masonryImages Masonry grid images (imageType='masonry') from page_gallery_images
     */
    public function __construct(
        public HeroSectionContent $heroSection,
        public StorytellingGradientSectionContent $gradientSection,
        public StorytellingIntroSplitSectionContent $introSplitSection,
        public StorytellingMasonrySectionContent $masonrySection,
        public GlobalUiContent $globalUiContent,
        public array $masonryImages = [],
    ) {
    }
}
