<?php
declare(strict_types=1);

namespace App\ViewModels\History;

use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Jazz\ArtistsData;
use App\ViewModels\Jazz\BookingCallToActionData;
use App\ViewModels\Jazz\PricingData;
use App\ViewModels\Jazz\ScheduleCallToActionData;
use App\ViewModels\Jazz\ScheduleData;
use App\ViewModels\Jazz\VenuesData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * ViewModel for the History page.
 *
 * Contains all pre-formatted data needed by the history page view.
 * The service prepares this data so the view only needs to render.
 */
final readonly class HistoryPageViewModel
{
    public function __construct(
        public HeroData $heroData,
        public GlobalUiData $globalUi,
        public GradientSectionData $gradientSection,
        public IntroSplitSectionData $introSplitSection,
        public RouteData $routeData,
        public VenuesData $venuesData,
        public TicketOptionsData $ticketOptionsData,
        public InfoAboutTourData $infoAboutTourData,
        public ScheduleData $scheduleData,
    )
    {
    }
}
