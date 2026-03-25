<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Pages\HomePageData;

/**
 * Contract for assembling the homepage's composite data model.
 * The returned HomePageData bundles CMS content, event types, map locations,
 * and a schedule preview so the controller can pass it straight to the mapper.
 */
interface IHomeService
{
    /**
     * Returns all data needed to build the home page.
     */
    public function getHomePageData(): HomePageData;
}
