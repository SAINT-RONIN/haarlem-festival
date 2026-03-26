<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\GlobalUiConstants;
use App\Constants\StorytellingPageConstants;
use App\Models\GlobalUiContent;
use App\Models\HeroSectionContent;
use App\Models\StorytellingGradientSectionContent;
use App\Models\StorytellingIntroSplitSectionContent;
use App\Models\StorytellingMasonrySectionContent;
use App\Models\StorytellingPageData;
use App\Repositories\Interfaces\ICmsContentRepository;
use App\Services\Interfaces\IStorytellingService;

class StorytellingService implements IStorytellingService
{
    public function __construct(
        private readonly ICmsContentRepository $cmsService,
    ) {
    }

    /**
     * Fetches all CMS sections needed to render the storytelling overview page.
     * The reason for this is because the service is the only layer allowed to call repositories and assemble the raw domain payload the mapper will later format.
     */
    public function getStorytellingPageData(): StorytellingPageData
    {
        $slug = StorytellingPageConstants::PAGE_SLUG;
        return new StorytellingPageData(
            heroSection:       HeroSectionContent::fromRawArray($this->cmsService->getHeroSectionContent($slug)),
            gradientSection:   StorytellingGradientSectionContent::fromRawArray($this->cmsService->getSectionContent($slug, StorytellingPageConstants::SECTION_GRADIENT)),
            introSplitSection: StorytellingIntroSplitSectionContent::fromRawArray($this->cmsService->getSectionContent($slug, StorytellingPageConstants::SECTION_INTRO_SPLIT)),
            masonrySection:    StorytellingMasonrySectionContent::fromRawArray($this->cmsService->getSectionContent($slug, StorytellingPageConstants::SECTION_MASONRY)),
            globalUiContent:   GlobalUiContent::fromRawArray($this->cmsService->getSectionContent(GlobalUiConstants::PAGE_SLUG, GlobalUiConstants::SECTION_KEY)),
        );
    }
}
