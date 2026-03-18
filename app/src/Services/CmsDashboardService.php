<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CmsRepository;
use App\Services\Interfaces\ICmsDashboardService;

/**
 * Service for CMS Dashboard operations.
 *
 * Returns raw domain data (CmsPage models and plain activity arrays).
 * ViewModel construction is the controller's responsibility via CmsDashboardMapper.
 */
class CmsDashboardService implements ICmsDashboardService
{
    public function __construct(
        private CmsRepository $cmsRepository,
    ) {
    }

    /**
     * Returns up to 4 recently updated pages plus static activity entries.
     *
     * @return array{recentPages: \App\Models\CmsPage[], activities: array[]}
     */
    public function getDashboardData(): array
    {
        return [
            'recentPages' => $this->loadRecentPages(),
            'activities'  => $this->buildActivityData(),
        ];
    }

    /**
     * Returns all pages for the pages management list.
     *
     * @return \App\Models\CmsPage[]
     */
    public function getPagesListData(): array
    {
        try {
            return $this->cmsRepository->findPages(['includeLastUpdated' => true]);
        } catch (\RuntimeException $e) {
            error_log('CMS pages list fetch failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * @return \App\Models\CmsPage[]
     */
    private function loadRecentPages(): array
    {
        try {
            $pages = $this->cmsRepository->findPages(['includeLastUpdated' => true]);
            return array_slice($pages, 0, 4);
        } catch (\RuntimeException $e) {
            error_log('CMS pages fetch failed: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * @return array[]
     */
    private function buildActivityData(): array
    {
        return [];
    }
}
