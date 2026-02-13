<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CmsRepository;
use App\Repositories\Interfaces\ICmsRepository;
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Repositories\Interfaces\IVenueRepository;
use App\Repositories\MediaAssetRepository;
use App\Repositories\VenueRepository;
use App\Services\Interfaces\IHistoryService;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\History\HistoryPageViewModel;
use App\ViewModels\History\ImportantInfoAboutTour;
use App\ViewModels\History\PricingCard;
use App\ViewModels\History\TicketOptions;
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
    // Map each historical route stop to its specific color
    private const BADGE_COLORS = [
        'stbavo'           => 'bg-sky-600/80',      // 1. Church of St.Bavo
        'grotemarkt'      => 'bg-orange-800/80',   // Grote Markt
        'dehallen'        => 'bg-amber-400/80',    // De Hallen
        'proveniershof'   => 'bg-lime-700/80',     // Proveniershof
        'jopenkerk'       => 'bg-violet-800/80',   // Jopenkerk
        'waalsekerk'      => 'bg-rose-500/80',     // Waalse Kerk
        'molendeadriaan'  => 'bg-lime-500/80',     // Molen de Adriaan
        'amsterdamsepoort'=> 'bg-stone-700/80',    // Amsterdamse Poort
        'hofvanbakenes'   => 'bg-orange-500/80',   // Hof van Bakenes
    ];
    private ICmsRepository $cmsRepository;
    private IMediaAssetRepository $mediaAssetRepository;

    private ISessionService $sessionService;
    private ?array $historyPageData = null;
    private ?array $historySections = null;
    private IVenueRepository $venueRepository;

    public function __construct()
    {
        $this->cmsRepository = new CmsRepository();
        $this->mediaAssetRepository = new MediaAssetRepository();
        $this->sessionService = new SessionService();
        $this->venueRepository = new VenueRepository();
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
            routeData: $this->buildRouteData(),
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

    /**
     * Gets a CMS-managed image URL for the History page.
     *
     * Supports both:
     * - MEDIA items (MediaAssetId)
     * - legacy IMAGE_PATH items (TextValue)
     */
    private function getCmsImage(string $sectionKey, string $itemKey, string $defaultUrl): string
    {
        if (!isset($this->historySections[$sectionKey])) {
            return $defaultUrl;
        }

        $sectionId = (int)$this->historySections[$sectionKey]['CmsSectionId'];
        $items = $this->cmsRepository->getItemsBySectionId($sectionId);

        foreach ($items as $item) {
            if ($item['ItemKey'] !== $itemKey) {
                continue;
            }

            if (!empty($item['MediaAssetId'])) {
                $media = $this->mediaAssetRepository->findById((int)$item['MediaAssetId']);
                $filePath = is_array($media) ? ($media['FilePath'] ?? null) : null;
                if (is_string($filePath) && $filePath !== '') {
                    return $filePath;
                }
            }

            $textPath = $item['TextValue'] ?? null;
            if (is_string($textPath) && $textPath !== '') {
                return $textPath;
            }

            return $defaultUrl;
        }

        return $defaultUrl;
    }

    private function buildHeroData(): HeroData
    {
        return new HeroData(
            mainTitle: $this->getCmsItem('hero_section', 'hero_main_title', 'A STROLL THROUGH HISTORY'),
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
        // Reuse the venue locations builder to provide data for the route list and map.
        $locations = $this->buildLocations();

        return new RouteData(
            locations: $locations,
        );
    }

    /**
     * Builds locations list for the History page from active venues.
     */
    private function buildLocations(): array
    {
        $locations = [];

        foreach ($this->venueRepository->findAllActive() as $venue) {
            $locations[] = $this->buildVenueLocation($venue);
        }

        return $locations;
    }

    /**
     * Builds location data for a single venue.
     */
    private function buildVenueLocation(array $venue): array
    {
        $routeKey = $this->determineVenueRouteKey($venue['Name']);

        return [
            'name'       => $venue['Name'],
            'address'    => $venue['AddressLine'],
            'routeKey'   => $routeKey,
            'badgeClass' => self::BADGE_COLORS[$routeKey] ?? 'bg-slate-800/60',
            'lat'        => $venue['Latitude'] ?? null,
            'lng'        => $venue['Longitude'] ?? null,
        ];
    }

    /**
     * Determines a stable route key for a historical stop based on venue name.
     */
    private function determineVenueRouteKey(string $venueName): string
    {
        $name = strtolower($venueName);

        if (str_contains($name, 'st. bavo') || str_contains($name, 'st bavo') || str_contains($name, 'bavo')) {
            return 'stbavo';
        }
        if (str_contains($name, 'grote markt')) {
            return 'grotemarkt';
        }
        if (str_contains($name, 'de hallen') || str_contains($name, 'hallen')) {
            return 'dehallen';
        }
        if (str_contains($name, 'proveniershof')) {
            return 'proveniershof';
        }
        if (str_contains($name, 'jopenkerk')) {
            return 'jopenkerk';
        }
        if (str_contains($name, 'waalse kerk') || str_contains($name, 'waalsekerk')) {
            return 'waalsekerk';
        }
        if (str_contains($name, 'molen de adriaan') || str_contains($name, 'de adriaan')) {
            return 'molendeadriaan';
        }
        if (str_contains($name, 'amsterdamse poort')) {
            return 'amsterdamsepoort';
        }
        if (str_contains($name, 'hof van bakenes') || str_contains($name, 'bakenes')) {
            return 'hofvanbakenes';
        }

        // Fallback color for any additional locations
        return array_key_first(self::BADGE_COLORS) ?: 'stbavo';
    }

    private function buildVenuesData(): VenuesData
    {
        $venues = [
            new VenueCardData(
                name: $this->getCmsItem(
                    'historical_locations_section',
                    'history_grotemarkt_name',
                    'Grote Markt'),
                description: $this->getCmsItem(
                    'historical_locations_section',
                    'history_grotemarkt_description',
                    'The heart of the historic center of Haarlem.'
                ),
                imageUrl: $this->getCmsImage(
                    'historical_locations_section',
                    'history_grotemarkt_image',
                    '/assets/Image/History/History-GroteMarkt.png'),
            ),
            new VenueCardData(
                name: $this->getCmsItem(
                    'historical_locations_section',
                    'history_amsterdamsepoort_name',
                    'Amsterdamse Poort'),
                description: $this->getCmsItem(
                    'historical_locations_section',
                    'history_amsterdamsepoort_description',
                    'The heart of the historic center of Haarlem.'
                ),
                imageUrl: $this->getCmsImage(
                    'historical_locations_section',
                    'history_amsterdamsepoort_image',
                    '/assets/Image/History/History-AmsterdamsePoort.png'),
            ),
            new VenueCardData(
                name: $this->getCmsItem(
                    'historical_locations_section',
                    'history_molendeadriaan_name',
                    'Molen De Adriaan'),
                description: $this->getCmsItem(
                    'historical_locations_section',
                    'history_molendeadriaan_description',
                    'The heart of the historic center of Haarlem.'
                ),
                imageUrl: $this->getCmsImage(
                    'historical_locations_section',
                    'history_molendeadriaan_image',
                    '/assets/Image/History/History-MolenDeAdriaan.png'),
            ),
        ];

        return new VenuesData(
            headingText: $this->getCmsItem(
                'historical_locations_section',
                'historical_locations_heading',
                'Read more about these locations'),
            venues: $venues,
        );
    }



    private function buildTicketOptionsData() : TicketOptions
    {
        return new TicketOptions(
            headingText: $this->getCmsItem(
                'ticket_options_section',
                'ticket_options_heading',
                'Your ticket options to join the experience'),
            pricingCards: [
                new PricingCard(
                    icon: $this->getCmsImage('history_ticket_options_section', 'history_single_ticket_icon', '/assets/Icons/History/History/single-ticket-icon.svg'),
                    title: $this->getCmsItem('history_ticket_options_section', 'history_pricing_single_title', 'Single Ticket'),
                    price: $this->getCmsItem('history_pricing_section', 'history_pricing_single_price', '€17.50'),
                    descriptionItems: [
                        $this->getCmsItem('history_pricing_section', 'history_pricing_single_include1', 'Per person'),
                        $this->getCmsItem('history_pricing_section', 'history_pricing_single_include2', 'Includes one complimentary drink'),
                        $this->getCmsItem('history_pricing_section', 'history_pricing_single_include3', '2.5 hour guided tour'),
                    ]
                ),
                new PricingCard(
                    icon: $this->getCmsImage(
                        'history_ticket_options_section', 'history_group_ticket_icon', '/assets/Icons/History/History/group-ticket-icon.svg'),
                    title: $this->getCmsItem('history_ticket_options_section', 'history_pricing_group_title', 'Group Ticket'),
                    price: $this->getCmsItem('history_pricing_section', 'history_pricing_group_price', '€60.00'),
                    descriptionItems: [
                        $this->getCmsItem('history_pricing_section', 'history_pricing_group_include1', 'For up to 4 people'),
                        $this->getCmsItem('history_pricing_section', 'history_pricing_group_include2', 'Includes four complimentary drinks'),
                        $this->getCmsItem('history_pricing_section', 'history_pricing_group_include3', 'Best value for families!'),
                    ]
                )
            ],
        );
    }

    private function buildInfoAboutTourData() : ImportantInfoAboutTour
    {
        return new ImportantInfoAboutTour(
            headingText: $this->getCmsItem(
                'history_important_tour_info_section',
                'history_important_tour_info_heading',
                'Important information about the tour'),
            infoItems: [
                $this->getCmsItem('history_important_tour_info_section', 'important_info_item1', 'Minimum age requirement: 12 years old'),
                $this->getCmsItem('history_important_tour_info_section', 'important_info_item2', 'No strollers allowed due to the nature of the walking route'),
                $this->getCmsItem('history_important_tour_info_section', 'important_info_item3', 'Tour duration: Approximately 2.5 hours including 15-minute break'),
                $this->getCmsItem('history_important_tour_info_section', 'important_info_item4', 'Group ticket is the best value for a group of 4 or for a family'),
                $this->getCmsItem('history_important_tour_info_section', 'important_info_item5', 'Starting point: Look for the giant flag near Church of St. Bavo at Grote Markt'),
                $this->getCmsItem('history_important_tour_info_section', 'important_info_item6', 'Group size: Maximum 12 participants per guide'),
                $this->getCmsItem('history_important_tour_info_section', 'important_info_item7', 'Comfortable walking shoes recommended'),
                $this->getCmsItem('history_important_tour_info_section', 'important_info_item8', 'Tours run in light rain; severe weather cancellations will be communicated via email'),
            ],
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
