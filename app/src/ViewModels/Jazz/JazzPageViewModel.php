<?php

declare(strict_types=1);

namespace App\ViewModels\Jazz;

use App\ViewModels\BaseViewModel;
use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

final readonly class JazzPageViewModel extends BaseViewModel
{
    public function __construct(
        HeroData $heroData,
        GlobalUiData $globalUi,
        array $cms,
        public GradientSectionData $gradientSection,
        public IntroSplitSectionData $introSplitSection,
        public VenuesData $venuesData,
        public PricingData $pricingData,
        public ScheduleCallToActionData $scheduleCtaData,
        public ArtistsData $artistsData,
        public ScheduleData $scheduleData,
        public BookingCallToActionData $bookingCtaData,
        public ?ScheduleSectionViewModel $scheduleSection = null,
    ) {
        parent::__construct(
            heroData: $heroData,
            globalUi: $globalUi,
            currentPage: $heroData->currentPage,
            cms: $cms,
            includeNav: false,
        );
    }
}
