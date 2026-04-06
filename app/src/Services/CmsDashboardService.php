<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Cms\CmsDashboardData;
use App\DTOs\Domain\Filters\CmsPageFilter;
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
     * Loads all CMS pages and returns a dashboard bundle with the four most recently updated ones.
     *
     * It returns a CmsDashboardData (not the raw page list) because the dashboard widget only
     * needs a small preview — not the complete list. The activities field is intentionally empty;
     * it is a placeholder reserved for a future activity-log feature, not an oversight.
     *
     * @throws CmsOperationException When loading dashboard data fails
     */
    public function getDashboardData(): CmsDashboardData
    {
        try {
            $pages = $this->cmsRepository->findPages(new CmsPageFilter(includeLastUpdated: true));

            return new CmsDashboardData(
                // The dashboard widget shows a fixed preview of the 4 most recently updated pages, not the full list.
                recentPages: array_slice($pages, 0, 4),
                // Activities feed is not built yet — this stays empty until the audit-log feature is added.
                activities: [],
            );
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to load dashboard data.', 0, $error);
        }
    }

    /**
     * Returns every CMS page with its last-updated timestamp, used to populate the pages list screen.
     *
     * It returns all pages (not a slice) because the pages list screen needs the full set so
     * editors can see and navigate to every page in the system.
     *
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
