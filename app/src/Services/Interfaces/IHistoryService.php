<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\DTOs\Pages\HistoryPageData;

/**
 * Interface for History page service.
 */
interface IHistoryService
{
    public function getHistoryPageData(): HistoryPageData;
}
