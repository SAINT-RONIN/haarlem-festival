<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

/**
 * Interface for Home page service.
 */
interface IHomeService
{
    /**
     * Returns raw data arrays needed to build the home page.
     */
    public function getHomePageData(): array;
}
