<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\StorytellingPageConstants;
use App\Models\HeroSectionContent;
use App\Models\GradientSectionContent;
use App\Models\IntroSectionContent;
use App\Models\StorytellingMasonrySectionContent;
use App\Models\StorytellingPageData;
use App\Repositories\Interfaces\ICmsContentRepository;
use App\Services\Interfaces\IStorytellingService;

/**
 * Composes the CMS-driven domain payload for the Storytelling overview page.
 *
 * Fetches hero, gradient, intro-split, masonry, and global-UI sections
 * from the CMS repository and bundles them into a StorytellingPageData object.
 */
class StorytellingService implements IStorytellingService
{
    public function __construct(
        private readonly ICmsContentRepository $cmsService,
        private readonly GlobalUiContentLoader $globalUiLoader,
    ) {
    }

    /**
     * Fetches all CMS sections needed to render the storytelling overview page.
     *
     * The service layer is the only place allowed to call repositories;
     * the returned payload is passed to the mapper layer for UI formatting.
     */
    public function getStorytellingPageData(): StorytellingPageData
    {
        $slug = StorytellingPageConstants::PAGE_SLUG;
        return new StorytellingPageData(
            heroSection:       HeroSectionContent::fromRawArray($this->cmsService->getHeroSectionContent($slug)),
            gradientSection:   GradientSectionContent::fromRawArray($this->cmsService->getSectionContent($slug, StorytellingPageConstants::SECTION_GRADIENT)),
            introSplitSection: IntroSectionContent::fromRawArray($this->cmsService->getSectionContent($slug, StorytellingPageConstants::SECTION_INTRO_SPLIT)),
            masonrySection:    StorytellingMasonrySectionContent::fromRawArray($this->cmsService->getSectionContent($slug, StorytellingPageConstants::SECTION_MASONRY)),
            globalUiContent:   $this->globalUiLoader->load(),
        );
    }
}
