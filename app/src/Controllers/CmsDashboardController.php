<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Constants\CmsMessages;
use App\Mappers\CmsDashboardViewMapper;
use App\Services\Interfaces\ICmsDashboardService;
use App\Services\Interfaces\ISessionService;
use App\View\ViewRenderer;

class CmsDashboardController extends CmsBaseController
{
    private const VIEW = __DIR__ . '/../Views/pages/cms/dashboard.php';

    public function __construct(
        ISessionService $sessionService,
        private readonly ICmsDashboardService $cmsDashboardService,
    ) {
        parent::__construct($sessionService);
    }

    public function index(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $this->renderDashboard();
        });
    }

    public function pages(): void
    {
        $this->handleCmsPageRequest(function (): void {
            $this->renderPages();
        });
    }

    private function renderDashboard(): void
    {
        ViewRenderer::render(self::VIEW, ['currentView' => 'dashboard', 'viewModel' => $this->dashboardViewModel()]);
    }

    private function renderPages(): void
    {
        $searchQuery = trim((string) filter_input(INPUT_GET, 'search'));
        ViewRenderer::render(self::VIEW, ['currentView' => 'pages', 'searchQuery' => $searchQuery, 'viewModel' => $this->pagesViewModel($searchQuery)]);
    }

    private function dashboardViewModel(): \App\ViewModels\Cms\DashboardViewModel
    {
        $data = $this->cmsDashboardService->getDashboardData();
        return CmsDashboardViewMapper::toDashboardViewModel($data->recentPages, $data->activities, $this->userName());
    }

    private function pagesViewModel(string $searchQuery): \App\ViewModels\Cms\PagesListViewModel
    {
        return CmsDashboardViewMapper::toPagesListViewModel($this->cmsDashboardService->getPagesListData(), $searchQuery, $this->userName());
    }

    private function userName(): string
    {
        $name = $this->sessionService->get('user_display_name', CmsMessages::DEFAULT_ADMIN_NAME);
        return is_string($name) && $name !== '' ? $name : CmsMessages::DEFAULT_ADMIN_NAME;
    }
}
