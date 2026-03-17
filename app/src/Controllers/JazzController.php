<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Controllers\Support\ControllerErrorResponder;
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
            $viewModel = JazzPageViewModel::fromDomainData($this->enrichWithSharedData($data));
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

    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function enrichWithSharedData(array $data): array
    {
        $globalUiResult = $this->cmsService->getGlobalUiContent();
        $data['globalUiContent'] = is_array($globalUiResult['content'] ?? null)
            ? $globalUiResult['content']
            : [];
        $data['isLoggedIn'] = $this->sessionService->isLoggedIn();

        return $data;
    }
}
