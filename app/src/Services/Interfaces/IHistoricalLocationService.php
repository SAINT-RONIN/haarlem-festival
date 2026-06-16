<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Domain\Pages\HistoricalLocationPageData;

/**
 * Interface for historical location page service.
 */
interface IHistoricalLocationService
{
    /**
     * @param string $pageSlug
     * @return HistoricalLocationPageData
     */
    public function getHistoralLocationPageData(string $pageSlug): HistoricalLocationPageData;
}
