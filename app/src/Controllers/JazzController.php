<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
use App\Mappers\CmsMapper;
use App\Services\JazzArtistDetailService;
use App\Services\Interfaces\ICmsService;
use App\Services\Interfaces\IJazzService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Jazz\JazzArtistDetailPageViewModel;
use App\ViewModels\Jazz\JazzPageViewModel;

/**
 * Controller for Jazz page.
 */
class JazzController extends BaseController
{
    public function __construct(
        private readonly IJazzService $jazzService,
        private readonly JazzArtistDetailService $jazzArtistDetailService,
        private readonly ICmsService $cmsService,
        private readonly ISessionService $sessionService,
    ) {
    }

    /**
     * Display the Jazz page.
     */
    public function index(): void
    {
        try {
            $data = $this->jazzService->getJazzPageData();
            $globalUi = CmsMapper::toGlobalUiData(
                $this->cmsService->getSectionContent('home', 'global_ui'),
                $this->sessionService->isLoggedIn(),
            );
            $viewModel = JazzPageViewModel::fromDomainData($data, $globalUi);
            $this->renderPage(__DIR__ . '/../Views/pages/jazz.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error);
        }
    }

    /**
     * Display a Jazz artist detail page by slug.
     */
    public function detail(string $slug): void
    {
        try {
            $data = $this->jazzArtistDetailService->getArtistPageDataBySlug($slug);
            $viewModel = JazzArtistDetailPageViewModel::fromDomainData($data);
            // Detail page uses a dedicated view model and does not rely on BaseViewModel globals.
            $this->renderView(__DIR__ . '/../Views/pages/jazz-artist-detail.php', $viewModel);
        } catch (\Throwable $error) {
            ControllerErrorResponder::respond($error, 404);
        }
    }

}
