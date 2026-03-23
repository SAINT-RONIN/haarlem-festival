<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CmsDashboardData;
use App\Models\CmsPageFilter;
use App\Repositories\Interfaces\ICmsRepository;
use App\Services\Interfaces\ICmsDashboardService;

/**
 * Provides data for the CMS admin dashboard and pages list.
 *
 * Fetches CMS pages sorted by last-updated timestamp, returning
 * either a trimmed set for the dashboard overview or the full list
 * for the pages management screen.
 */
class CmsDashboardService implements ICmsDashboardService
{
    public function __construct(
        private readonly ICmsRepository $cmsRepository,
    ) {
    }

    /**
     * Returns the four most recently updated pages for the dashboard summary.
     *
     * @return CmsDashboardData with recentPages limited to 4 and an empty activities list (placeholder for future audit log)
     */
    public function getDashboardData(): CmsDashboardData
    {
        $pages = $this->cmsRepository->findPages(new CmsPageFilter(includeLastUpdated: true));

        return new CmsDashboardData(
            recentPages: array_slice($pages, 0, 4),
            activities: [],
        );
    }

    /**
     * @return \App\Models\CmsPage[]
     */
    public function getPagesListData(): array
    {
        return $this->cmsRepository->findPages(new CmsPageFilter(includeLastUpdated: true));
    }
}
