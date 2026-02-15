<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\ViewModels\Jazz\JazzPageViewModel;

/**
 * Interface for Jazz page service.
 */
interface IJazzService
{
    /**
     * Builds the Jazz page view model with all required data.
     *
     * @return JazzPageViewModel Prepared data for the Jazz view
     */
    public function getJazzPageData(): JazzPageViewModel;
}
