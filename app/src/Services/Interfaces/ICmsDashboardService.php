<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\CmsDashboardData;

interface ICmsDashboardService
{
    public function getDashboardData(): CmsDashboardData;

    /**
     * @return \App\Models\CmsPage[]
     */
    public function getPagesListData(): array;
}
