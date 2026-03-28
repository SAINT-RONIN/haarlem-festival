<?php

declare(strict_types=1);

namespace App\ViewModels\History;

use App\ViewModels\BaseViewModel;
use App\ViewModels\GlobalUiData;
use App\ViewModels\HeroData;
use App\ViewModels\History\LocationHero;

final readonly class HistoricalLocationViewModel extends BaseViewModel
{
    public function __construct(
        HeroData $heroData,
        GlobalUiData $globalUi,
        public LocationHero $locationHero,
        public LocationIntroduction $locationIntroduction,
        public LocationFacts $locationFacts,
        public LocationSignificance $locationSignificance,
    ) {
        parent::__construct(
            heroData: $heroData,
            globalUi: $globalUi,
            currentPage: $heroData->currentPage,
            includeNav: false,
        );
    }
}
