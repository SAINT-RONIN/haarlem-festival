<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\EventSessionRepository;
use App\Repositories\EventTypeRepository;
use App\Repositories\HistoryRepository;
use App\Repositories\Interfaces\IHistoryRepository;
use App\Repositories\VenueRepository;
use App\Services\CmsService;
use App\Services\Interfaces\IHistoryService;
use App\ViewModels\HistoryPageViewModel;
use App\ViewModels\HomePageViewModel;

/**
 * Service for preparing history page data.
 *
 * Assembles all data needed for the history view, including
 * event types, locations, and schedule information.
 */
class HistoryService implements IHistoryService
{
    private EventTypeRepository $eventTypeRepository;
    private VenueRepository $venueRepository;
    private EventSessionRepository $eventSessionRepository;
    private CmsService $cmsService;
    private IHistoryRepository $historyRepository;
    private const DEFAULT_IMAGE = '/assets/Image/Image (History).png';
    private const VALID_IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif', 'heic'];

    public function __construct()
    {
        $this->eventTypeRepository = new EventTypeRepository();
        $this->venueRepository = new VenueRepository();
        $this->eventSessionRepository = new EventSessionRepository();
        $this->cmsService = new CmsService();
        $this->historyRepository = new HistoryRepository();
    }

    private const SCHEDULE_COLORS = [
        '1' => 'bg-azure-blue',
        '2' => 'bg-deep-crimson',
        '3' => 'bg-amber-gold',
        '4' => 'bg-olive-green',
        '5' => 'bg-deep-purple',
        '6' => 'bg-aqua',
        '7' => 'bg-blue',
        '8' => 'bg-green',
        '9' => 'bg-red'
    ];

    /**
     * Builds the homepage view model with all required data.
     */
    public function getHistoryPageData(): HistoryPageViewModel
    {
        $cmsContent = $this->cmsService->getHomePageContent();

        return new HistoryPageViewModel();
    }

}
