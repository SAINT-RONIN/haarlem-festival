<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CmsDashboardData;
use App\Models\CmsPageFilter;
use App\Repositories\Interfaces\ICmsRepository;
use App\Services\Interfaces\ICmsDashboardService;

class CmsDashboardService implements ICmsDashboardService
{
    public function __construct(
        private ICmsRepository $cmsRepository,
    ) {
    }

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
