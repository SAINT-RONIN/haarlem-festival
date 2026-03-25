<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\StorytellingPageConstants;
use App\DTOs\Pages\StorytellingPageData;
use App\Repositories\GlobalContentRepository;
use App\Repositories\StorytellingContentRepository;
use App\Exceptions\PageLoadException;
use App\Services\Interfaces\IStorytellingService;

/**
 * Composes the CMS-driven domain payload for the Storytelling overview page.
 *
 * Fetches hero, gradient, intro-split, masonry, and global-UI sections
 * from the content repositories and bundles them into a StorytellingPageData object.
 */
class StorytellingService implements IStorytellingService
{
    public function __construct(
        private readonly GlobalContentRepository $globalContentRepo,
        private readonly StorytellingContentRepository $storyContentRepo,
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
        try {
            return $this->assembleStorytellingPageData();
        } catch (\Throwable $error) {
            throw new PageLoadException('Failed to load the Storytelling page.', 0, $error);
        }
    }

    /** Fetches all CMS sections and assembles the storytelling page data. */
    private function assembleStorytellingPageData(): StorytellingPageData
    {
        $slug = StorytellingPageConstants::PAGE_SLUG;
        return new StorytellingPageData(
            heroSection:       $this->globalContentRepo->findHeroContent($slug),
            gradientSection:   $this->globalContentRepo->findGradientContent($slug, StorytellingPageConstants::SECTION_GRADIENT),
            introSplitSection: $this->globalContentRepo->findIntroContent($slug, StorytellingPageConstants::SECTION_INTRO_SPLIT),
            masonrySection:    $this->storyContentRepo->findMasonryContent($slug, StorytellingPageConstants::SECTION_MASONRY),
            globalUiContent:   $this->globalUiLoader->load(),
        );
    }
}
