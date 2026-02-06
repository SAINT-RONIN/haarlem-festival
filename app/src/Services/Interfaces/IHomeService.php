<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\ViewModels\HomePageViewModel;

/**
 * Interface for Home page service.
 */
interface IHomeService
{
    /**
     * Builds the homepage view model with all required data.
     *
     * @return HomePageViewModel Prepared data for the home view
     */
    public function getHomePageData(): HomePageViewModel;
}
