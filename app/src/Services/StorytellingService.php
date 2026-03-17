<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\StorytellingPageConstants;
use App\Enums\EventTypeId;
use App\Models\StorytellingPageData;
use App\Services\Interfaces\ICmsService;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\IStorytellingService;

class StorytellingService implements IStorytellingService
{
    public function __construct(
        private readonly ICmsService $cmsService,
        private readonly IScheduleService $scheduleService,
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
            scheduleSectionData: $this->scheduleService->getScheduleData(
                StorytellingPageConstants::PAGE_SLUG,
                EventTypeId::Storytelling->value,
                StorytellingPageConstants::SCHEDULE_MAX_DAYS,
            ),
        );
    }
}
