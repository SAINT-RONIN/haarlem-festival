<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\StorytellingPageConstants;
use App\Models\StorytellingPageData;
use App\Repositories\Interfaces\ICmsContentRepository;
use App\Services\Interfaces\IStorytellingService;

class StorytellingService implements IStorytellingService
{
    public function __construct(
        private readonly ICmsContentRepository $cmsService,
    ) {
    }

    public function getStorytellingPageData(): StorytellingPageData
    {
        $pageSlug = StorytellingPageConstants::PAGE_SLUG;

        return new StorytellingPageData(
            sections: [
                StorytellingPageConstants::SECTION_HERO => $this->cmsService->getHeroSectionContent($pageSlug),
                StorytellingPageConstants::SECTION_GRADIENT => $this->cmsService->getSectionContent(
                    $pageSlug,
                    StorytellingPageConstants::SECTION_GRADIENT,
                ),
                StorytellingPageConstants::SECTION_INTRO_SPLIT => $this->cmsService->getSectionContent(
                    $pageSlug,
                    StorytellingPageConstants::SECTION_INTRO_SPLIT,
                ),
                StorytellingPageConstants::SECTION_MASONRY => $this->cmsService->getSectionContent(
                    $pageSlug,
                    StorytellingPageConstants::SECTION_MASONRY,
                ),
            ],
        );
    }
}
