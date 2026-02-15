<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CmsRepository;
use App\Services\Interfaces\ICmsDashboardService;
use App\Utils\TimeFormatter;
use App\ViewModels\Cms\ActivityViewModel;
use App\ViewModels\Cms\DashboardViewModel;
use App\ViewModels\Cms\PageListItemViewModel;
use App\ViewModels\Cms\PagesListViewModel;
use App\ViewModels\Cms\RecentPageViewModel;

/**
 * Service for CMS Dashboard operations.
 *
 * Contains business logic for dashboard and pages list views.
 * Returns ViewModels ready for rendering.
 */
class CmsDashboardService implements ICmsDashboardService
{
    private CmsRepository $cmsRepository;

    public function __construct()
    {
        $this->cmsRepository = new CmsRepository();
    }

    /**
     * Gets dashboard data including recent pages and activities.
     */
    public function getDashboardData(string $userName): DashboardViewModel
    {
        return new DashboardViewModel(
            recentPages: $this->getRecentPages(),
            activities: $this->getRecentActivities(),
            userName: $userName,
        );
    }

    /**
     * Gets pages list data for the pages management view.
     */
    public function getPagesListData(string $searchQuery, string $userName): PagesListViewModel
    {
        return new PagesListViewModel(
            pages: $this->getAllPages(),
            searchQuery: $searchQuery,
            userName: $userName,
        );
    }

    /**
     * Gets recently updated pages for the dashboard.
     *
     * @return RecentPageViewModel[]
     */
    private function getRecentPages(): array
    {
        try {
            $cmsPages = $this->cmsRepository->findAllPages();
            $pages = $this->mapPagesToRecentViewModels(array_slice($cmsPages, 0, 4));

            return !empty($pages) ? $pages : $this->getDefaultRecentPages();
        } catch (\RuntimeException $e) {
            error_log('CMS pages fetch failed: ' . $e->getMessage());
            return $this->getDefaultRecentPages();
        }
    }

    /**
     * Maps CMS page arrays to RecentPageViewModel instances.
     *
     * @param array<int, array{Title: string, UpdatedAtUtc: ?string}> $cmsPages
     * @return RecentPageViewModel[]
     */
    private function mapPagesToRecentViewModels(array $cmsPages): array
    {
        $viewModels = [];
        foreach ($cmsPages as $page) {
            $viewModels[] = new RecentPageViewModel(
                title: $page['Title'],
                status: 'Published',
                timeAgo: TimeFormatter::formatTimeAgo($page['UpdatedAtUtc'] ?? null),
            );
        }
        return $viewModels;
    }

    /**
     * Returns fallback data when database is unavailable.
     *
     * @return RecentPageViewModel[]
     */
    private function getDefaultRecentPages(): array
    {
        return [
            new RecentPageViewModel('Home', 'Published', '2h ago'),
            new RecentPageViewModel('Jazz', 'Published', 'yesterday'),
            new RecentPageViewModel('Dance', 'Published', '3d ago'),
            new RecentPageViewModel('History', 'Draft', '6d ago'),
        ];
    }

    /**
     * Gets all pages for the pages list view.
     *
     * @return PageListItemViewModel[]
     */
    private function getAllPages(): array
    {
        try {
            $cmsPages = $this->cmsRepository->findAllPages();
            $pages = $this->mapPagesToListViewModels($cmsPages);

            return !empty($pages) ? $pages : $this->getDefaultAllPages();
        } catch (\RuntimeException $e) {
            error_log('CMS pages list fetch failed: ' . $e->getMessage());
            return $this->getDefaultAllPages();
        }
    }

    /**
     * Maps CMS page arrays to PageListItemViewModel instances.
     *
     * @param array<int, array{CmsPageId: int, Title: string, Slug: string, UpdatedAtUtc: ?string}> $cmsPages
     * @return PageListItemViewModel[]
     */
    private function mapPagesToListViewModels(array $cmsPages): array
    {
        $viewModels = [];
        foreach ($cmsPages as $page) {
            $viewModels[] = new PageListItemViewModel(
                id: (int)$page['CmsPageId'],
                title: $page['Title'],
                slug: $page['Slug'],
                status: 'Published',
                updatedAt: TimeFormatter::formatTimeAgo($page['UpdatedAtUtc'] ?? null),
            );
        }
        return $viewModels;
    }

    /**
     * Returns fallback pages list.
     *
     * @return PageListItemViewModel[]
     */
    private function getDefaultAllPages(): array
    {
        return [
            new PageListItemViewModel(1, 'Home', 'home', 'Published', '2h ago'),
            new PageListItemViewModel(2, 'Jazz', 'jazz', 'Published', 'yesterday'),
            new PageListItemViewModel(3, 'Dance', 'dance', 'Published', '3d ago'),
            new PageListItemViewModel(4, 'History', 'history', 'Draft', '6d ago'),
            new PageListItemViewModel(5, 'Restaurant', 'restaurant', 'Published', '1w ago'),
            new PageListItemViewModel(6, 'Storytelling', 'storytelling', 'Published', '1w ago'),
        ];
    }

    /**
     * Gets recent activity feed for the dashboard.
     *
     * @return ActivityViewModel[]
     */
    private function getRecentActivities(): array
    {
        // Static activities - could be enhanced to pull from activity log table
        return [
            new ActivityViewModel('edit', "You updated 'Home'", '2h ago', 'blue'),
            new ActivityViewModel('file-text', "Draft saved: 'History'", 'yesterday', 'amber'),
            new ActivityViewModel('image', 'Media uploaded: header.jpg', '3d ago', 'purple'),
            new ActivityViewModel('user', "User 'Editor' role updated", '1w ago', 'green'),
        ];
    }
}
