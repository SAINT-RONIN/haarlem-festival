<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Constants\StorytellingPageConstants;
use App\Controllers\Support\ControllerErrorResponder;
use App\Mappers\CmsMapper;
use App\Services\StorytellingDetailService;
use App\Services\Interfaces\ICmsService;
use App\Services\Interfaces\ISessionService;
use App\Services\Interfaces\IStorytellingService;
use App\ViewModels\Storytelling\StorytellingDetailPageViewModel;
use App\ViewModels\Storytelling\StorytellingPageViewModel;

class StorytellingController extends BaseController
{
    public function __construct(
        private readonly IStorytellingService $storytellingService,
        private readonly StorytellingDetailService $storytellingDetailService,
        private readonly ICmsService $cmsService,
        private readonly ISessionService $sessionService,
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
            $viewModel = StorytellingPageViewModel::fromDomainData($pageData, $heroData, $globalUi);
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
            $viewModel = StorytellingDetailPageViewModel::fromDomainData($pageData, $globalUi);
            $this->renderPage(__DIR__ . '/../Views/pages/storytelling-detail.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
