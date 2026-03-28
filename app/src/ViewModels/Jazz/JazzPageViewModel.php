<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

use App\ViewModels\BaseViewModel;
use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * View data for the public jazz landing page (jazz.php).
 *
 * Carries all section data: hero, intro, artists, venues, schedule, pricing.
 */
final readonly class JazzPageViewModel extends BaseViewModel
{
    public function __construct(
        HeroData $heroData,
        GlobalUiData $globalUi,
        public GradientSectionData $gradientSection,
        public IntroSplitSectionData $introSplitSection,
        public VenuesData $venuesData,
        public PricingData $pricingData,
        public ScheduleCallToActionData $scheduleCtaData,
        public ArtistsData $artistsData,
        public BookingCallToActionData $bookingCtaData,
        public ?ScheduleSectionViewModel $scheduleSection = null,
    ) {
        parent::__construct(
            heroData: $heroData,
            globalUi: $globalUi,
            currentPage: $heroData->currentPage,
            includeNav: false,
        );
    }
}
