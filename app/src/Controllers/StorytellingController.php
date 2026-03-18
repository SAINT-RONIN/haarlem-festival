<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Constants\StorytellingDetailConstants;
use App\Constants\StorytellingPageConstants;
use App\Controllers\Support\ControllerErrorResponder;
use App\Enums\EventTypeId;
use App\Mappers\CmsMapper;
use App\Mappers\ScheduleMapper;
use App\Mappers\StorytellingMapper;
use App\Services\Interfaces\ICmsPageContentService;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\ISessionService;
use App\Services\Interfaces\IStorytellingDetailService;
use App\Services\Interfaces\IStorytellingService;

class StorytellingController extends BaseController
{
    public function __construct(
        private readonly IStorytellingService $storytellingService,
        private readonly IStorytellingDetailService $storytellingDetailService,
        private readonly ICmsPageContentService $cmsService,
        private readonly ISessionService $sessionService,
        private readonly IScheduleService $scheduleService,
    ) {
    }

    /**
     * GET /storytelling
     */
    public function index(): void
    {
        try {
            $pageData = $this->storytellingService->getStorytellingPageData();
            $heroContent = $pageData->sections[StorytellingPageConstants::SECTION_HERO] ?? [];
            $heroData = CmsMapper::toHeroData($heroContent, StorytellingPageConstants::CURRENT_PAGE);
            $globalUi = CmsMapper::toGlobalUiData(
                $this->cmsService->getSectionContent('home', 'global_ui'),
                $this->sessionService->isLoggedIn(),
            );
            $scheduleData = $this->scheduleService->getScheduleData(
                StorytellingPageConstants::PAGE_SLUG,
                EventTypeId::Storytelling->value,
                StorytellingPageConstants::SCHEDULE_MAX_DAYS,
            );
            $scheduleSection = ScheduleMapper::toScheduleSection($scheduleData);
            $viewModel = StorytellingMapper::toPageViewModel($pageData, $heroData, $globalUi, $scheduleSection);
            $this->renderPage(__DIR__ . '/../Views/pages/storytelling.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * GET /storytelling/{id}
     */
    public function detail(string $id): void
    {
        try {
            $eventId = (int)$id;
            $pageData = $this->storytellingDetailService->getDetailPageData($eventId);
            $globalUi = CmsMapper::toGlobalUiData(
                $this->cmsService->getSectionContent('home', 'global_ui'),
                $this->sessionService->isLoggedIn(),
            );
            $scheduleData = $this->scheduleService->getScheduleData(
                StorytellingDetailConstants::SCHEDULE_PAGE_SLUG,
                EventTypeId::Storytelling->value,
                StorytellingDetailConstants::SCHEDULE_MAX_DAYS,
                $eventId,
            );
            $scheduleSection = ScheduleMapper::toScheduleSection($scheduleData);
            $viewModel = StorytellingMapper::toDetailPageViewModel($pageData, $globalUi, $scheduleSection);
            $this->renderPage(__DIR__ . '/../Views/pages/storytelling-detail.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
