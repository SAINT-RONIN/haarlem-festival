<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\ViewModels\History\HistoryPageViewModel;

/**
 * Interface for History page service.
 */
interface IHistoryService
{
    /**
     * Builds the homepage view model with all required data.
     *
     * @return HistoryPageViewModel Prepared data for the history view
     */
    public function getHistoryPageData(): array;
}
