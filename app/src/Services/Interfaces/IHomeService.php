<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\HomePageData;

/**
 * Interface for Home page service.
 */
interface IHomeService
{
    /**
     * Returns all data needed to build the home page.
     */
    public function getHomePageData(): HomePageData;
}
