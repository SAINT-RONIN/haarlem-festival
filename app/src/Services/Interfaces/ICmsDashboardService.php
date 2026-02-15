<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\ViewModels\Cms\DashboardViewModel;
use App\ViewModels\Cms\PagesListViewModel;

/**
 * Interface for CMS Dashboard service operations.
 */
interface ICmsDashboardService
{
    /**
     * Gets dashboard data including recent pages and activities.
     */
    public function getDashboardData(string $userName): DashboardViewModel;

    /**
     * Gets pages list data for the pages management view.
     */
    public function getPagesListData(string $searchQuery, string $userName): PagesListViewModel;
}

