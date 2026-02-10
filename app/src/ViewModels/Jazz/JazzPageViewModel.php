<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;

/**
 * ViewModel for the Jazz page.
 */
final readonly class JazzPageViewModel
{
    public function __construct(
        public HeroData $heroData,
        public GlobalUiData $globalUi,
        public GradientSectionData $gradientSection,
        public IntroSplitSectionData $introSplitSection,
        public VenuesData $venuesData,
        public PricingData $pricingData,
        public ScheduleCallToActionData $scheduleCtaData,
        public ArtistsData $artistsData,
        public ScheduleData $scheduleData,
        public BookingCallToActionData $bookingCtaData,
    ) {
    }
}
