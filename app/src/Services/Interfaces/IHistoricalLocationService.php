<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\ViewModels\History\HistoricalLocationViewModel;

/**
 * Interface for historical location page service.
 */
interface IHistoricalLocationService
{
    /**
     * Builds the historical location view model with all required data.
     *
     * @return HistoricalLocationViewModel Prepared data for the historical location view
     */
    public function getHistoralLocationData(string $name): ?HistoricalLocationViewModel;
}
