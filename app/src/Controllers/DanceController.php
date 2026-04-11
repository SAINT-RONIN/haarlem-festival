<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Constants\DancePageConstants;
use App\Constants\ScheduleConstants;
use App\Enums\EventTypeId;
use App\Mappers\DanceMapper;
use App\Mappers\ScheduleMapper;
use App\Services\Interfaces\IDanceService;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * Dance listing page with filterable schedule.
 */
class DanceController extends BaseController
{
    public function __construct(
        private readonly IDanceService $danceService,
        ISessionService $sessionService,
        private readonly IScheduleService $scheduleService,
    ) {
        parent::__construct($sessionService);
    }

    public function index(): void
    {
        $this->handlePageRequest(function (): void {
            $data            = $this->danceService->getDancePageData();
            $scheduleSection = $this->buildListingScheduleSection();
            $viewModel       = DanceMapper::toPageViewModel($data, $scheduleSection, $this->isLoggedIn());
            $this->renderPage(__DIR__ . '/../Views/pages/dance.php', $viewModel);
        });
    }

    private function buildListingScheduleSection(): ScheduleSectionViewModel
    {
        $scheduleData = $this->scheduleService->getScheduleData(
            DancePageConstants::PAGE_SLUG,
            EventTypeId::Dance->value,
            ScheduleConstants::MAX_DAYS,
            filterParams: $this->readScheduleFilterParams(),
        );
        return ScheduleMapper::toScheduleSection($scheduleData);
    }
}
