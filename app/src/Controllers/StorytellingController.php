<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Services\Interfaces\IStorytellingService;
use App\Services\StorytellingService;
use App\ViewModels\Storytelling\StorytellingDetailPageViewModel;
use App\ViewModels\Storytelling\StorytellingPageViewModel;

class StorytellingController extends BaseController
{
    private IStorytellingService $storytellingService;

    public function __construct()
    {
        $this->storytellingService = new StorytellingService();
    }

    /**
     * GET /storytelling
     */
    public function index(): void
    {
        try {
            $data = $this->storytellingService->getStorytellingPageData();
            $viewModel = StorytellingPageViewModel::fromData(...$data);
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
            $data = $this->storytellingService->getStorytellingDetailPageData($eventId);
            $viewModel = StorytellingDetailPageViewModel::fromEventData(...$data);
            $this->renderView(__DIR__ . '/../Views/pages/storytelling-detail.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }
}
