<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Constants\DanceArtistDetailConstants;
use App\Constants\DancePageConstants;
use App\Enums\EventTypeId;
use App\Exceptions\DanceArtistDetailNotFoundException;
use App\Mappers\DanceMapper;
use App\Mappers\ScheduleMapper;
use App\Repositories\ArtistAlbumRepository;
use App\Repositories\ArtistGalleryImageRepository;
use App\Repositories\ArtistHighlightRepository;
use App\Repositories\CmsContentRepository;
use App\Repositories\EventRepository;
use App\Services\DanceArtistDetailService;
use App\Services\DanceService;
use App\Services\ScheduleService;
use App\Services\SessionService;
use Throwable;

final class DanceController extends BaseController
{
    private DanceService $danceService;
    private DanceArtistDetailService $danceArtistDetailService;
    private SessionService $sessionService;
    private ScheduleService $scheduleService;

    public function __construct()
    {
        $cmsContentRepository = new CmsContentRepository();
        $eventRepository = new EventRepository();
        $artistAlbumRepository = new ArtistAlbumRepository();
        $artistHighlightRepository = new ArtistHighlightRepository();
        $artistGalleryImageRepository = new ArtistGalleryImageRepository();

        $this->danceService = new DanceService($cmsContentRepository);
        $this->danceArtistDetailService = new DanceArtistDetailService(
            $cmsContentRepository,
            $eventRepository,
            $artistAlbumRepository,
            $artistHighlightRepository,
            $artistGalleryImageRepository,
        );
        $this->sessionService = new SessionService();
        $this->scheduleService = new ScheduleService();
    }

    public function index(): void
    {
        try {
            $data = $this->danceService->getDancePageData();
            $scheduleSection = $this->buildListingScheduleSection();
            $viewModel = DanceMapper::toPageViewModel(
                $data,
                $scheduleSection,
                $this->sessionService->isLoggedIn()
            );

            $this->renderPage(__DIR__ . '/../Views/pages/dance.php', $viewModel);
        } catch (Throwable $error) {
            http_response_code(500);
            require __DIR__ . '/../Views/pages/errors/500.php';
        }
    }

    public function detail(string $slug): void
    {
        try {
            $pageData = $this->danceArtistDetailService->getArtistPageDataBySlug($slug);

            $scheduleData = $this->scheduleService->getScheduleData(
                DanceArtistDetailConstants::SCHEDULE_PAGE_SLUG,
                EventTypeId::Dance->value,
                DanceArtistDetailConstants::SCHEDULE_MAX_DAYS,
                $pageData->eventId,
            );

            $performances = ScheduleMapper::flattenEventsAsViewModels($scheduleData);
            $viewModel = DanceMapper::toArtistDetailViewModel($pageData, $performances);

            $this->renderView(__DIR__ . '/../Views/pages/dance-artist-detail.php', $viewModel);
        } catch (DanceArtistDetailNotFoundException) {
            http_response_code(404);
            require __DIR__ . '/../Views/pages/errors/404.php';
        } catch (Throwable $error) {
            http_response_code(500);
            require __DIR__ . '/../Views/pages/errors/500.php';
        }
    }

    private function buildListingScheduleSection()
    {
        $scheduleData = $this->scheduleService->getScheduleData(
            DancePageConstants::PAGE_SLUG,
            EventTypeId::Dance->value,
            DancePageConstants::SCHEDULE_MAX_DAYS,
            filterParams: $this->readScheduleFilterParams(),
        );

        return ScheduleMapper::toScheduleSection($scheduleData);
    }
}