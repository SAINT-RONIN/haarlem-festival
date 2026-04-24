<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Cms\CmsDashboardData;
use App\DTOs\Domain\Filters\CmsPageFilter;
use App\Exceptions\CmsOperationException;
use App\Repositories\Interfaces\ICmsRepository;
use App\Services\Interfaces\ICmsDashboardService;

class CmsDashboardService implements ICmsDashboardService
{
    public function __construct(
        private readonly ICmsRepository $cmsRepository,
    ) {}

    // activities is empty — placeholder for a future audit-log feature.
    /** @throws CmsOperationException */
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

    /** @throws CmsOperationException */
    public function getPagesListData(): array
    {
        try {
            return $this->cmsRepository->findPages(new CmsPageFilter(includeLastUpdated: true));
        } catch (\Throwable $error) {
            throw new CmsOperationException('Failed to load pages list.', 0, $error);
        }
    }
}
