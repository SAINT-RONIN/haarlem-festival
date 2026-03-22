<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Constants\JazzArtistDetailConstants;
use App\Constants\JazzPageConstants;
use App\Enums\EventTypeId;
use App\Exceptions\JazzArtistDetailNotFoundException;
use App\Mappers\CmsMapper;
use App\Mappers\JazzMapper;
use App\Mappers\ScheduleMapper;
use App\Models\GlobalUiContent;
use App\Services\Interfaces\ICmsPageContentService;
use App\Services\Interfaces\IJazzArtistDetailService;
use App\Services\Interfaces\IJazzService;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\ISessionService;

/**
 * Controller for Jazz page.
 */
class JazzController extends BaseController
{
    public function __construct(
        private readonly IJazzService $jazzService,
        private readonly IJazzArtistDetailService $jazzArtistDetailService,
        private readonly ICmsPageContentService $cmsService,
        private readonly ISessionService $sessionService,
        private readonly IScheduleService $scheduleService,
    ) {
    }

    /**
     * Display the Jazz page.
     */
    public function index(): void
    {
        $filterParams = $this->readScheduleFilterParams();
        $data = $this->jazzService->getJazzPageData();
        $globalUi = CmsMapper::toGlobalUiData(
            GlobalUiContent::fromRawArray(
                $this->cmsService->getSectionContent('home', 'global_ui'),
            ),
            $this->sessionService->isLoggedIn(),
        );
        $scheduleData = $this->scheduleService->getScheduleData(
            JazzPageConstants::PAGE_SLUG,
            EventTypeId::Jazz->value,
            JazzPageConstants::SCHEDULE_MAX_DAYS,
            filterParams: $filterParams,
        );
        $scheduleSection = ScheduleMapper::toScheduleSection($scheduleData);
        $viewModel = JazzMapper::toPageViewModel($data, $globalUi, $scheduleSection);
        $this->renderPage(__DIR__ . '/../Views/pages/jazz.php', $viewModel);
    }

    /**
     * Display a Jazz artist detail page by slug.
     */
    public function detail(string $slug): void
    {
        try {
            $pageData = $this->jazzArtistDetailService->getArtistPageDataBySlug($slug);
            $scheduleData = $this->scheduleService->getScheduleData(
                JazzArtistDetailConstants::SCHEDULE_PAGE_SLUG,
                EventTypeId::Jazz->value,
                JazzArtistDetailConstants::SCHEDULE_MAX_DAYS,
                $pageData->eventId,
            );
            $performances = ScheduleMapper::flattenEvents($scheduleData);
            $viewModel = JazzMapper::toArtistDetailViewModel($pageData, $performances);
            $this->renderView(__DIR__ . '/../Views/pages/jazz-artist-detail.php', $viewModel);
        } catch (JazzArtistDetailNotFoundException) {
            http_response_code(404);
            require __DIR__ . '/../Views/pages/errors/404.php';
        }
    }

}
