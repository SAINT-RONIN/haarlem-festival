<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\StorytellingPageConstants;
use App\Models\StorytellingPageData;
use App\Repositories\Interfaces\ICmsContentRepository;
use App\Repositories\Interfaces\IPageGalleryImageRepository;
use App\Services\Interfaces\IStorytellingService;

class StorytellingService implements IStorytellingService
{
    private const CMS_PAGE_ID_STORYTELLING = 2;

    public function __construct(
        private readonly ICmsContentRepository $cmsService,
        private readonly IPageGalleryImageRepository $pageGalleryImageRepository,
    ) {
    }

    /**
     * Fetches all CMS sections needed to render the storytelling overview page.
     * The reason for this is because the service is the only layer allowed to call repositories and assemble the raw domain payload the mapper will later format.
     */
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
            // TODO: change 'home' to 'global' after running the database migration in docs/global-ui-migration.md
            globalUiContent: $this->cmsService->getSectionContent('home', 'global_ui'),
            masonryImages: $this->pageGalleryImageRepository->findByPageId(
                self::CMS_PAGE_ID_STORYTELLING,
                'masonry',
            ),
        );
    }
}
