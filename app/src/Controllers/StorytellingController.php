<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
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
            $sharedData = $this->getSharedData();
            $viewModel = StorytellingPageViewModel::fromDomainData($pageData, $sharedData);
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
            $sharedData = $this->getSharedData();
            $viewModel = StorytellingDetailPageViewModel::fromDomainData($pageData, $sharedData);
            $this->renderPage(__DIR__ . '/../Views/pages/storytelling-detail.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * @return array{globalUiContent: array<string, mixed>, isLoggedIn: bool}
     */
    private function getSharedData(): array
    {
        $globalUiResult = $this->cmsService->getGlobalUiContent();
        return [
            'globalUiContent' => is_array($globalUiResult['content'] ?? null)
                ? $globalUiResult['content']
                : [],
            'isLoggedIn' => $this->sessionService->isLoggedIn(),
        ];
    }
}
