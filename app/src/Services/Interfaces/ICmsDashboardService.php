<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\CmsDashboardData;

/**
 * Defines the contract for assembling CMS dashboard and page overview data.
 */
interface ICmsDashboardService
{
    /**
     * Aggregates statistics and recent activity into a single dashboard payload.
     */
    public function getDashboardData(): CmsDashboardData;

    /**
     * Returns all CMS pages for the pages management list.
     *
     * @return \App\Models\CmsPage[]
     */
    public function getPagesListData(): array;
}
