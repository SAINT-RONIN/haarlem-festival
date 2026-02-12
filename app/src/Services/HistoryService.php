<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CmsRepository;
use App\Repositories\MediaAssetRepository;
use App\Services\Interfaces\IHistoryService;
use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\History\HistoryPageViewModel;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\History\ScheduleData;
use App\ViewModels\History\ScheduleDayData;
use App\ViewModels\History\ScheduleEventData;
use App\ViewModels\History\RouteData;
use App\ViewModels\History\VenueCardData;
use App\ViewModels\History\VenuesData;

/**
 * Service for preparing history page data.
 *
 * Assembles all data needed for the history view, including
 * event types, locations, and schedule information.
 */
class HistoryService implements IHistoryService
{
    private CmsRepository $cmsRepository;
    private MediaAssetRepository $mediaAssetRepository;
    private ScheduleService $scheduleService;
    private SessionService $sessionService;
    private ?array $historyPageData = null;
    private ?array $historySections = null;

    public function __construct()
    {
        $this->cmsRepository = new CmsRepository();
        $this->mediaAssetRepository = new MediaAssetRepository();
        $this->scheduleService = new ScheduleService();
        $this->sessionService = new SessionService();
    }

    /**
     * Builds the homepage view model with all required data.
     */
    public function getHistoryPageData(): HistoryPageViewModel
    {
        // Load page and sections once
        $this->loadPageData();

        return new HistoryPageViewModel(
            heroData: $this->buildHeroData(),
            globalUi: $this->buildGlobalUi(),
            gradientSection: $this->buildGradientSection(),
            introSplitSection: $this->buildIntroSplitSection(),
            routeData: $this->buildRouteData(),//TODO: implement route data building
            venuesData: $this->buildVenuesData(),
            ticketOptionsData: $this->buildTicketOptionsData(),
            infoAboutTourData: $this->buildInfoAboutTourData(),
            scheduleData: $this->buildScheduleData(),
        );
    }

    private function loadPageData(): void
    {
        if ($this->historyPageData === null) {
            $this->historyPageData = $this->cmsRepository->getPageBySlug('history');
            if ($this->historyPageData) {
                $sections = $this->cmsRepository->getSectionsByPageId((int)$this->historyPageData['CmsPageId']);
                $this->historyPageData = [];
                foreach ($sections as $section) {
                    $this->historySections[$section['SectionKey']] = $section;
                }
            }
        }
    }

    private function getCmsItem(string $sectionKey, string $itemKey, string $default = ''): string
    {
        if (!isset($this->historySections[$sectionKey])) {
            return $default;
        }

        $sectionId = (int)$this->historySections[$sectionKey]['CmsSectionId'];
        $items = $this->cmsRepository->getItemsBySectionId($sectionId);

        foreach ($items as $item) {
            if ($item['ItemKey'] === $itemKey) {
                $value = $item['TextValue'] ?? $item['HtmlValue'] ?? $default;
                return is_string($value) ? $value : $default;
            }
        }

        return $default;
    }

    private function buildHeroData(): HeroData
    {
        return new HeroData(
            mainTitle: $this->getCmsItem('hero_section', 'hero_main_title', 'HAARLEM HISTORY'),
            subtitle: $this->getCmsItem(
                'hero_section',
                'hero_subtitle',
                'Explore nine centuries of turbulent history, magnificent architecture, and cultural treasures'),
            primaryButtonText: $this->getCmsItem('hero_section', 'hero_button_primary', 'Explore the tour'),
            primaryButtonLink: $this->getCmsItem('hero_section', 'hero_button_primary_link', '#route'),
            secondaryButtonText: $this->getCmsItem('hero_section', 'hero_button_secondary', 'Get tickets'),
            secondaryButtonLink: $this->getCmsItem('hero_section', 'hero_button_secondary_link', '#tickets'),
            backgroundImageUrl: '/assets/Image/History/History-hero.png',
            currentPage: 'history',
        );
    }

    private function buildGlobalUi(): GlobalUiData
    {
        return new GlobalUiData(
            siteName: 'Haarlem Festival',
            navHome: 'Home',
            navJazz: 'Jazz',
            navDance: 'Dance',
            navHistory: 'History',
            navRestaurant: 'Restaurant',
            navStorytelling: 'Storytelling',
            btnMyProgram: 'My Program',
            isLoggedIn: $this->sessionService->isLoggedIn(),
        );
    }

    private function buildGradientSection(): GradientSectionData
    {
        return new GradientSectionData(
            headingText: $this->getCmsItem(
                'gradient_section',
                'gradient_heading',
                'Every street holds echoes of the past, shaped by the people who once walked there.'),
            subheadingText: $this->getCmsItem(
                'gradient_section',
                'gradient_subheading',
                'Where history comes alive through places, paths, and people.'),
            backgroundImageUrl: '/assets/Image/History/History-second-section.png',
        );
    }

    private function buildIntroSplitSection(): IntroSplitSectionData
    {
        $bodyText = $this->getCmsItem(
            'intro_section',
            'intro_body',
            'A Stroll Through History invites visitors to explore  rich past of Haarlem');

        return new IntroSplitSectionData(
            headingText: $this->getCmsItem(
                'intro_section',
                'intro_heading',
                'Experience the living history of Haarlem'),
            bodyText: $bodyText,
            imageUrl: '/assets/Image/History/History-third-section.png',
            imageAltText: 'A corner of a historic building in Haarlem',
        );
    }

    private function buildRouteData(): RouteData
    {
        return new RouteData;
    }

    private function buildVenuesData(): VenuesData
    {
        $venues = [
            new VenueCardData(
                name: $this->getCmsItem('historical_locations_section', 'history_grotemarkt_name', 'Grote Markt'),
                description: $this->getCmsItem(
                    'historical_locations_section',
                    'history_grotemarkt_description',
                    'The heart of the historic center of Haarlem.'
                ),
                imageUrl: $this->getCmsImage('historical_locations_section', 'history_grotemarkt_image', '/assets/Image/History/History-GroteMarkt.png'),
            ),
            new VenueCardData(
                name: $this->getCmsItem('historical_locations_section', 'history_grotemarkt_name', 'Grote Markt'),
                description: $this->getCmsItem(
                    'historical_locations_section',
                    'history_grotemarkt_description',
                    'The heart of the historic center of Haarlem.'
                ),
                imageUrl: $this->getCmsImage('historical_locations_section', 'history_grotemarkt_image', '/assets/Image/History/History-GroteMarkt.png'),
            ),
            new VenueCardData(
                name: $this->getCmsItem('historical_locations_section', 'history_grotemarkt_name', 'Grote Markt'),
                description: $this->getCmsItem(
                    'historical_locations_section',
                    'history_grotemarkt_description',
                    'The heart of the historic center of Haarlem.'
                ),
                imageUrl: $this->getCmsImage('historical_locations_section', 'history_grotemarkt_image', '/assets/Image/History/History-GroteMarkt.png'),
            ),
        ];

        return new ArtistsData(
            headingText: $this->getCmsItem('artists_section', 'artists_heading', 'Discover our lineup'),
            artists: $artists,
            currentPage: 1,
            totalPages: 4,
            totalArtists: 12,
        );
    }

    private function buildScheduleData(): ScheduleData
    {
        // Schedule data remains hardcoded for now (as per requirements)
        $thursday = new ScheduleDayData(
            dayName: 'Thursday',
            fullDate: 'Thursday, July 25',
            events: [
                new ScheduleEventData(
                    '10:00',
                    ['In English', 'In Dutch'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 25',
                    'Group ticket - best value for 4 people',
                    'from €17.50'),
                new ScheduleEventData(
                    '13:00',
                    ['In English', 'In Dutch'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 25',
                    'Group ticket - best value for 4 people',
                    'from €17.50'),
                new ScheduleEventData(
                    '16:00',
                    ['In English', 'In Dutch'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 25',
                    'Group ticket - best value for 4 people',
                    'from €17.50'),
            ],
        );

        $friday = new ScheduleDayData(
            dayName: 'Friday',
            fullDate: 'Friday, July 26',
            events: [
                new ScheduleEventData(
                    '10:00',
                    ['In English', 'In Dutch'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 26',
                    'Group ticket - best value for 4 people',
                    'from €17.50'),
                new ScheduleEventData(
                    '13:00',
                    ['In English', 'In Dutch', 'In Chinese'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 26',
                    'Group ticket - best value for 4 people',
                    'from €17.50'),
                new ScheduleEventData(
                    '16:00',
                    ['In English', 'In Dutch'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 26',
                    'Group ticket - best value for 4 people',
                    'from €17.50'),
            ],
        );

        $saturday = new ScheduleDayData(
            dayName: 'Saturday',
            fullDate: 'Saturday, July 27',
            events: [
                new ScheduleEventData(
                    '10:00',
                    ['In English', 'In Dutch'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 27',
                    'Group ticket - best value for 4 people',
                    'from €17.50'),
                new ScheduleEventData(
                    '13:00',
                    ['In English', 'In Dutch'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 27',
                    'Group ticket - best value for 4 people',
                    'from €17.50'),
                new ScheduleEventData(
                    '16:00',
                    ['In English', 'In Dutch', 'In Chinese'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 27',
                    'Group ticket - best value for 4 people',
                    'from €17.50'),
            ],
        );

        $sunday = new ScheduleDayData(
            dayName: 'Sunday',
            fullDate: 'Sunday, July 29',
            events: [
                new ScheduleEventData(
                    '10:00',
                    ['In English', 'In Dutch'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 29',
                    'Group ticket - best value for 4 people',
                    'from €17.50'),
                new ScheduleEventData(
                    '13:00',
                    ['In English', 'In Dutch'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 29',
                    'Group ticket - best value for 4 people',
                    'from €17.50'),
                new ScheduleEventData(
                    '16:00',
                    ['In English', 'In Dutch'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 29',
                    'Group ticket - best value for 4 people',
                    'from €17.50'),
            ],
        );

        return new ScheduleData(
            headingText: 'Tour schedule',
            filterLabel: 'Filters',
            days: [$thursday, $friday, $saturday, $sunday],
        );
    }
}
