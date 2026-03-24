<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\HistoricalLocationPageData;

/**
 * Interface for historical location page service.
 */
interface IHistoricalLocationService
{
    public function getHistoralLocationPageData(string $name): HistoricalLocationPageData;
}
