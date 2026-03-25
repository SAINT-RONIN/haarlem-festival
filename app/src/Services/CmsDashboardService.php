<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Cms\CmsDashboardData;
use App\DTOs\Filters\CmsPageFilter;
use App\Exceptions\CmsOperationException;
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
    /** @throws CmsOperationException When loading dashboard data fails */
    public function getDashboardData(): CmsDashboardData
    {
        try {
            $pages = $this->cmsRepository->findPages(new CmsPageFilter(includeLastUpdated: true));

            return new CmsDashboardData(
                recentPages: array_slice($pages, 0, 4),
                activities: [],
            );
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to load dashboard data.', 0, $error);
        }
    }

    /**
     * @return \App\Models\CmsPage[]
     * @throws CmsOperationException When loading pages list fails
     */
    public function getPagesListData(): array
    {
        try {
            return $this->cmsRepository->findPages(new CmsPageFilter(includeLastUpdated: true));
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to load pages list.', 0, $error);
        }
    }
}
