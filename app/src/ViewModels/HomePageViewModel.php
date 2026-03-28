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
    /**
     * @param HomeEventTypeViewModel[]   $eventTypes   Display-ready event type rows from HomeMapper
     * @param HomeLocationViewModel[]    $locations    Display-ready location rows from HomeMapper
     * @param HomeScheduleDayViewModel[] $scheduleDays Display-ready schedule day rows from HomeMapper
     */
    public function __construct(
        HeroData $heroData,
        GlobalUiData $globalUi,
        public HomeExploreBannerViewModel $exploreBanner,
        public HomeIntroSectionViewModel $introSection,
        public HomeEventsHeaderViewModel $eventsHeader,
        public HomeLocationsSectionViewModel $locationsSection,
        public HomeScheduleSectionViewModel $schedulePreviewSection,
        public array $eventTypes = [],
        public array $locations = [],
        public array $scheduleDays = [],
    ) {
        parent::__construct(
            heroData: $heroData,
            globalUi: $globalUi,
            currentPage: $heroData->currentPage,
            includeNav: false,
        );
    }
}
