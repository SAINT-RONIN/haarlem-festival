<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Interfaces\IHistoricalLocationService;
use App\ViewModels\History\HistoricalLocationViewModel;

class HistoricalLocationService implements IHistoricalLocationService{
    public function getHistoralLocationData(string $name): ?HistoricalLocationViewModel
    {
        // Load page and sections once
        $this->loadPageData($name);

        return new HistoricalLocationViewModel(

        );
    }
}
