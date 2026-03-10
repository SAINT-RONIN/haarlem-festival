<?php

declare(strict_types=1);

namespace App\ViewModels;

/**
 * ViewModel for the homepage.
 *
 * Contains all pre-formatted data needed by the home page view.
 * The service prepares this data so the view only needs to loop and print.
 */
final readonly class HomePageViewModel extends BaseViewModel
{
    public function __construct(
        HeroData $heroData,
        GlobalUiData $globalUi,
        public array        $eventTypes = [],
        public array        $locations = [],
        public array        $scheduleDays = [],
        public array        $cmsContent = [],
    ) {
        parent::__construct(
            heroData: $heroData,
            globalUi: $globalUi,
            currentPage: $heroData->currentPage,
            includeNav: false,
        );
    }
}
