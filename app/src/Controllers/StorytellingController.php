<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Constants\StorytellingDetailConstants;
use App\Constants\StorytellingPageConstants;
use App\Enums\EventTypeId;
use App\Exceptions\StorytellingEventNotFoundException;
use App\Mappers\ScheduleMapper;
use App\Mappers\StorytellingMapper;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\ISessionService;
use App\Services\Interfaces\IStorytellingDetailService;
use App\Services\Interfaces\IStorytellingService;
use App\Controllers\Support\ControllerErrorResponder;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

class StorytellingController extends BaseController
{
    public function __construct(
        private readonly IStorytellingService $storytellingService,
        private readonly IStorytellingDetailService $storytellingDetailService,
        private readonly ISessionService $sessionService,
        private readonly IScheduleService $scheduleService,
    ) {
    }

    /**
     * Renders the storytelling listing page.
     * The reason for this is because all page entry points live in the controller — it coordinates the service call, mapping, and view render without owning any logic.
     */
    public function index(): void
    {
        try {
            $pageData = $this->storytellingService->getStorytellingPageData();
            $scheduleSection = $this->buildListingScheduleSection();
            $isLoggedIn = $this->sessionService->isLoggedIn();
            $viewModel = StorytellingMapper::toPageViewModel($pageData, $scheduleSection, $isLoggedIn);
            $this->renderPage(__DIR__ . '/../Views/pages/storytelling.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    private function buildListingScheduleSection(): ScheduleSectionViewModel
    {
        $scheduleData = $this->scheduleService->getScheduleData(
            StorytellingPageConstants::PAGE_SLUG,
            EventTypeId::Storytelling->value,
            StorytellingPageConstants::SCHEDULE_MAX_DAYS,
            filterParams: $this->readScheduleFilterParams(),
        );
        return ScheduleMapper::toScheduleSection($scheduleData);
    }

    /**
     * Renders the detail page for a single storytelling event.
     * The reason for this is because each event has its own CMS-driven detail page that requires fetching both event data and a filtered schedule.
     */
    public function detail(string $slug): void
    {
        try {
            $this->renderDetailPage($slug);
        } catch (StorytellingEventNotFoundException) {
            http_response_code(404);
            require __DIR__ . '/../Views/pages/errors/404.php';
        }
    }

    private function renderDetailPage(string $slug): void
    {
        $pageData = $this->storytellingDetailService->getDetailPageData($slug);
        $scheduleSection = $this->buildDetailScheduleSection($pageData->event->eventId, $pageData->scheduleCtaButtonText ?: null);
        $isLoggedIn = $this->sessionService->isLoggedIn();
        $viewModel = StorytellingMapper::toDetailPageViewModel($pageData, $scheduleSection, $isLoggedIn);
        $this->renderPage(__DIR__ . '/../Views/pages/storytelling-detail.php', $viewModel);
    }

    private function buildDetailScheduleSection(int $eventId, ?string $ctaButtonText): ScheduleSectionViewModel
    {
        $scheduleData = $this->scheduleService->getScheduleData(
            StorytellingDetailConstants::SCHEDULE_PAGE_SLUG,
            EventTypeId::Storytelling->value,
            StorytellingDetailConstants::SCHEDULE_MAX_DAYS,
            $eventId,
            $ctaButtonText,
        );
        return ScheduleMapper::toScheduleSection($scheduleData);
    }
}
