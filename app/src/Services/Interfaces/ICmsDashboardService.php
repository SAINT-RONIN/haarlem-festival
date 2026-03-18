<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

/**
 * Interface for CMS Dashboard service operations.
 */
interface ICmsDashboardService
{
    /**
     * Returns up to 4 recently updated pages plus static activity entries.
     *
     * @return array{recentPages: \App\Models\CmsPage[], activities: array[]}
     */
    public function getDashboardData(): array;

    /**
     * Returns all CmsPage models for the pages management list.
     *
     * @return \App\Models\CmsPage[]
     */
    public function getPagesListData(): array;
}
