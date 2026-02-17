<?php

declare(strict_types=1);

namespace App\ViewModels\History;

use App\ViewModels\BaseViewModel;
use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;

/**
 * Aggregates all data required to render the History landing page.
 *
 * Contains all pre-formatted data needed by the history page view.
 * The service prepares this data so the view only needs to render.
 */
final readonly class HistoryPageViewModel extends BaseViewModel
{
    public function __construct(
        public HeroData $heroData,
        GlobalUiData $globalUi,
        public GradientSectionData $gradientSection,
        public IntroSplitSectionData $introSplitSection,
        public RouteData $routeData,
        public VenuesData $venuesData,
        public TicketOptions $ticketOptionsData,
        public ImportantInfoAboutTour $infoAboutTourData,
        public ScheduleData $scheduleData,
    ) {
        parent::__construct($globalUi);
    }
}
