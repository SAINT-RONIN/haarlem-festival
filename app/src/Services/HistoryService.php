<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EventTypeId;
use App\Models\CmsItem;
use App\Models\CmsSection;
use App\Repositories\CmsRepository;
use App\Repositories\Interfaces\ICmsRepository;
use App\Repositories\Interfaces\IMediaAssetRepository;
use App\Repositories\Interfaces\IVenueRepository;
use App\Repositories\MediaAssetRepository;
use App\Repositories\VenueRepository;
use App\Services\Interfaces\IHistoryService;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\ISessionService;
use App\ViewModels\Age\AgeLabelFormatter;
use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\History\HistoryPageViewModel;
use App\ViewModels\History\ImportantInfoAboutTour;
use App\ViewModels\History\PricingCard;
use App\ViewModels\History\RouteData;
use App\ViewModels\History\RouteVenue;
use App\ViewModels\History\ScheduleCard;
use App\ViewModels\History\ScheduleData;
use App\ViewModels\History\ScheduleDayData;
use App\ViewModels\History\TicketOptions;
use App\ViewModels\History\VenueCardData;
use App\ViewModels\History\VenuesData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * Service for preparing history page data.
 *
 * Assembles all data needed for the history view, including
 * event types, locations, and schedule information.
 */
class HistoryService implements IHistoryService
{
    private ICmsRepository $cmsRepository;
    private IMediaAssetRepository $mediaAssetRepository;

    private ISessionService $sessionService;
    private ?int $historyPageId = null;
    /** @var array<string, CmsSection>|null */
    private ?array $historySections = null;
    /** @var array<string, list<CmsItem>>|null */
    private ?array $historyItemsBySection = null;
    private IVenueRepository $venueRepository;
    private IScheduleService $scheduleService;

    public function __construct()
    {
        $this->cmsRepository = new CmsRepository();
        $this->mediaAssetRepository = new MediaAssetRepository();
        $this->sessionService = new SessionService();
        $this->venueRepository = new VenueRepository();
        $this->scheduleService = new ScheduleService();
    }

    /**
     * Builds the history page view model with all required data.
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
            scheduleSection: $this->buildScheduleSection(),
        );
    }

    /**
     * Builds the detail page ViewModel for a historical location.
     * Returns null if the location is not found.
     *
     * ?????All detail content comes from the Restaurant domain table columns.
     * Images come from MediaAsset JOINs in the repository.
     */
    public function getHistoralLocationData(int $id): ?HistoricalLocationViewModel
    {
        // Load page and sections once
        $this->loadPageData();
    }

    /**
     * Loads and caches CMS page and sections for the history page.
     */
    private function loadPageData(): void
    {
        if ($this->historyPageId !== null) {
            return;
        }

        $pages = $this->cmsRepository->findPages(['slug' => 'history']);
        if ($pages === []) {
            return;
        }

        $this->historyPageId = (int)$pages[0]['CmsPageId'];
        $sections = $this->cmsRepository->findSections(['cmsPageId' => $this->historyPageId]);
        $this->historySections = [];
        foreach ($sections as $section) {
            /** @var CmsSection $section */
            $this->historySections[$section->sectionKey] = $section;
        }

        $items = $this->cmsRepository->findItems(['cmsPageId' => $this->historyPageId]);
        $itemsBySectionId = [];
        foreach ($items as $item) {
            $itemsBySectionId[$item->cmsSectionId][] = $item;
        }

        $this->historyItemsBySection = [];
        foreach ($this->historySections as $sectionKey => $section) {
            $this->historyItemsBySection[$sectionKey] = $itemsBySectionId[$section->cmsSectionId] ?? [];
        }
    }

    /**
     * Retrieve a CMS-managed text/HTML item for the history page.
     *
     * @param string $sectionKey The CMS section key.
     * @param string $itemKey The item key inside the section.
     * @param string $default Fallback value if no CMS item is found.
     */
    private function getCmsItem(string $sectionKey, string $itemKey, string $default = ''): string
    {
        if (!isset($this->historySections[$sectionKey])) {
            return $default;
        }

        $items = $this->historyItemsBySection[$sectionKey] ?? [];

        foreach ($items as $item) {
            /** @var CmsItem $item */
            if ($item->itemKey === $itemKey) {
                $value = $item->textValue ?? $item->htmlValue ?? $default;
                return is_string($value) ? $value : $default;
            }
        }

        return $default;
    }

    /**
     * Gets a CMS-managed image URL for the History page.
     *
     * Supports both MEDIA items (MediaAssetId) and legacy IMAGE_PATH items (TextValue).
     *
     * @param string $sectionKey
     * @param string $itemKey
     * @param string $defaultUrl
     * @return string
     */
    private function getCmsImage(string $sectionKey, string $itemKey, string $defaultUrl): string
    {
        if (!isset($this->historySections[$sectionKey])) {
            return $defaultUrl;
        }

        $items = $this->historyItemsBySection[$sectionKey] ?? [];

        foreach ($items as $item) {
            /** @var CmsItem $item */
            if ($item->itemKey !== $itemKey) {
                continue;
            }

            if ($item->mediaAssetId !== null) {
                $media = $this->mediaAssetRepository->findById($item->mediaAssetId);
                if ($media !== null && $media->filePath !== '') {
                    return $media->filePath;
                }
            }

            $textPath = $item->textValue ?? null;
            if (is_string($textPath) && $textPath !== '') {
                return $textPath;
            }

            return $defaultUrl;
        }

        return $defaultUrl;
    }

    /**
     * Builds hero section data for the history page.
     */
    private function buildHeroData(): HeroData
    {
        return new HeroData(
            mainTitle: $this->getCmsItem('hero_section', 'hero_main_title', 'A STROLL THROUGH HISTORY'),
            subtitle: $this->getCmsItem(
                'hero_section',
                'hero_subtitle',
                'Explore nine centuries of turbulent history, magnificent architecture, and cultural treasures'
            ),
            primaryButtonText: $this->getCmsItem('hero_section', 'hero_button_primary', 'Explore the tour'),
            primaryButtonLink: $this->getCmsItem('hero_section', 'hero_button_primary_link', '#route'),
            secondaryButtonText: $this->getCmsItem('hero_section', 'hero_button_secondary', 'Get tickets'),
            secondaryButtonLink: $this->getCmsItem('hero_section', 'hero_button_secondary_link', '#tickets'),
            backgroundImageUrl: '/assets/Image/History/History-hero.png',
            currentPage: 'history',
        );
    }

    /**
     * Builds global UI navigation and button labels.
     */
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
            loginLabel: 'Login',
            logoutLabel: 'Logout',
            isLoggedIn: $this->sessionService->isLoggedIn(),
        );
    }

    /**
     * Builds the gradient overlay section content.
     */
    private function buildGradientSection(): GradientSectionData
    {
        return new GradientSectionData(
            headingText: $this->getCmsItem(
                'gradient_section',
                'gradient_heading',
                'Every street holds echoes of the past, shaped by the people who once walked there.'
            ),
            subheadingText: $this->getCmsItem(
                'gradient_section',
                'gradient_subheading',
                'Where history comes alive through places, paths, and people.'
            ),
            backgroundImageUrl: '/assets/Image/History/History-second-section.png',
        );
    }

    /**
     * Builds the split intro section with text and supporting image.
     */
    private function buildIntroSplitSection(): IntroSplitSectionData
    {
        $bodyText = $this->getCmsItem(
            'intro_section',
            'intro_body',
            'A Stroll Through History invites visitors to explore  rich past of Haarlem'
        );

        return new IntroSplitSectionData(
            headingText: $this->getCmsItem(
                'intro_section',
                'intro_heading',
                'Experience the living history of Haarlem'
            ),
            bodyText: $bodyText,
            imageUrl: '/assets/Image/History/History-third-section.png',
            imageAltText: 'A corner of a historic building in Haarlem',
        );
    }

    /**
     * Builds the route data for the historical walking tour.
     *
     * @return RouteData Contains route heading, ordered venues and map image.
     */
    private function buildRouteData(): RouteData
    {
        $locations = [
            new RouteVenue(
                venueName: $this->getCmsItem(
                    'route_section',
                    'route_location1_name',
                    'Church of St.Bavo'
                ),
                venueBadgeColor: 'bg-sky-600/80',
                venueDescription: $this->getCmsItem(
                    'route_section',
                    'route_location1_description',
                    'A monumental Gothic church famed for its towering nave and historic Müller organ once played by Mozart.'
                ),
            ),
            new RouteVenue(
                venueName: $this->getCmsItem(
                    'route_section',
                    'route_location2_name',
                    'Grote Markt'
                ),
                venueBadgeColor: 'bg-orange-800/80',
                venueDescription: $this->getCmsItem(
                    'route_section',
                    'route_location2_description',
                    'A vibrant central square surrounded by landmark buildings and lively cafés;  the city’s cultural heart.'
                ),
            ),
            new RouteVenue(
                venueName: $this->getCmsItem(
                    'route_section',
                    'route_location3_name',
                    'De Hallen'
                ),
                venueBadgeColor: 'bg-amber-400/80',
                venueDescription: $this->getCmsItem(
                    'route_section',
                    'route_location3_description',
                    'A former meat hall turned into an art and photography museum space that hosts exhibitions as part of the Frans Hals Museum.'
                ),
            ),
            new RouteVenue(
                venueName: $this->getCmsItem(
                    'route_section',
                    'route_location4_name',
                    'Proveniershof'
                ),
                venueBadgeColor: 'bg-lime-700/80',
                venueDescription: $this->getCmsItem(
                    'route_section',
                    'route_location4_description',
                    'A peaceful 18th-century hofje (courtyard community) offering a quiet oasis with historic almshouses.'
                ),
            ),
            new RouteVenue(
                venueName: $this->getCmsItem(
                    'route_section',
                    'route_location5_name',
                    'Jopenkerk'
                ),
                venueBadgeColor: 'bg-violet-800/80',
                venueDescription: $this->getCmsItem(
                    'route_section',
                    'route_location5_description',
                    'A former church transformed into Haarlem’s iconic craft brewery and restaurant, blending tradition with modern beer culture.'
                ),
            ),
            new RouteVenue(
                venueName: $this->getCmsItem(
                    'route_section',
                    'route_location6_name',
                    'Waalse Kerk'
                ),
                venueBadgeColor: 'bg-rose-500/80',
                venueDescription: $this->getCmsItem(
                    'route_section',
                    'route_location6_description',
                    'An intimate 17th-century Walloon church known for its serene atmosphere and historic interior.'
                ),
            ),
            new RouteVenue(
                venueName: $this->getCmsItem(
                    'route_section',
                    'route_location7_name',
                    'Molen de Adriaan'
                ),
                venueBadgeColor: 'bg-lime-500/80',
                venueDescription: $this->getCmsItem(
                    'route_section',
                    'route_location7_description',
                    'A reconstructed 18th-century riverside windmill offering tours and panoramic views over the Spaarne.'
                ),
            ),
            new RouteVenue(
                venueName: $this->getCmsItem(
                    'route_section',
                    'route_location8_name',
                    'Amsterdamse Poort'
                ),
                venueBadgeColor: 'bg-stone-700/80',
                venueDescription: $this->getCmsItem(
                    'route_section',
                    'route_location8_description',
                    'Haarlem’s last surviving medieval city gate, showcasing impressive brickwork and centuries of history.'
                ),
            ),
            new RouteVenue(
                venueName: $this->getCmsItem(
                    'route_section',
                    'route_location9_name',
                    'Hof van Bakenes'
                ),
                venueBadgeColor: 'bg-orange-500/80',
                venueDescription: $this->getCmsItem(
                    'route_section',
                    'route_location9_description',
                    'The oldest hofje in the Netherlands, featuring charming gardens and classic courtyard architecture dating back to 1395.'
                ),
            ),
        ];

        return new RouteData(
            headingText: $this->getCmsItem(
                'route_section',
                'route_heading',
                'The Route'
            ),
            venues: $locations,
            mapImagePath: $this->getCmsImage(
                'route_section',
                'route_map_image',
                '/assets/Image/History/History-RouteMap.png'
            )
        );
    }

    /**
     * Builds the "Read more about these locations" venues section.
     *
     * @return VenuesData Card data for a curated subset of route venues.
     */
    private function buildVenuesData(): VenuesData
    {
        $venues = [
            new VenueCardData(
                name: $this->getCmsItem(
                    'historical_locations_section',
                    'history_grotemarkt_name',
                    'Grote Markt'
                ),
                description: $this->getCmsItem(
                    'historical_locations_section',
                    'history_grotemarkt_description',
                    'The heart of the historic center of Haarlem.'
                ),
                imageUrl: $this->getCmsImage(
                    'historical_locations_section',
                    'history_grotemarkt_image',
                    '/assets/Image/History/History-GroteMarkt.png'
                ),
            ),
            new VenueCardData(
                name: $this->getCmsItem(
                    'historical_locations_section',
                    'history_amsterdamsepoort_name',
                    'Amsterdamse Poort'
                ),
                description: $this->getCmsItem(
                    'historical_locations_section',
                    'history_amsterdamsepoort_description',
                    'As the only remaining city gate.'
                ),
                imageUrl: $this->getCmsImage(
                    'historical_locations_section',
                    'history_amsterdamsepoort_image',
                    '/assets/Image/History/History-AmsterdamsePoort.png'
                ),
            ),
            new VenueCardData(
                name: $this->getCmsItem(
                    'historical_locations_section',
                    'history_molendeadriaan_name',
                    'Molen De Adriaan'
                ),
                description: $this->getCmsItem(
                    'historical_locations_section',
                    'history_molendeadriaan_description',
                    'A striking riverside windmill.'
                ),
                imageUrl: $this->getCmsImage(
                    'historical_locations_section',
                    'history_molendeadriaan_image',
                    '/assets/Image/History/History-MolenDeAdriaan.png'
                ),
            ),
        ];

        return new VenuesData(
            headingText: $this->getCmsItem(
                'historical_locations_section',
                'historical_locations_heading',
                'Read more about these locations'
            ),
            venues: $venues,
        );
    }

    /**
     * Builds ticket options (single and group pricing cards) for the tour.
     */
    private function buildTicketOptionsData(): TicketOptions
    {
        return new TicketOptions(
            headingText: $this->getCmsItem(
                'ticket_options_section',
                'ticket_options_heading',
                'Your ticket options to join the experience'
            ),
            pricingCards: [
                new PricingCard(
                    icon: $this->getCmsImage('history_ticket_options_section', 'history_single_ticket_icon', '/assets/Icons/History/single-ticket-icon.svg'),
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
                        'history_ticket_options_section',
                        'history_group_ticket_icon',
                        '/assets/Icons/History/group-ticket-icon.svg'
                    ),
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

    /**
     * Builds the "Important information about the tour" bullet list.
     */
    private function buildInfoAboutTourData(): ImportantInfoAboutTour
    {
        return new ImportantInfoAboutTour(
            headingText: $this->getCmsItem(
                'history_important_tour_info_section',
                'history_important_tour_info_heading',
                'Important information about the tour'
            ),
            infoItems: [
                $this->getCmsItem(
                    'history_important_tour_info_section',
                    'important_info_item1',
                    AgeLabelFormatter::formatRequirement(12, null)
                ),
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

    /**
     * Builds the hardcoded schedule for historical route.
     */
    private function buildScheduleData(): ScheduleData
    {
        // Schedule data remains hardcoded for now (as per requirements)
        $thursday = new ScheduleDayData(
            dayName: 'Thursday',
            fullDate: 'Thursday, July 25',
            events: [
                new ScheduleCard(
                    '10:00',
                    ['In English', 'In Dutch'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 25',
                    'Group ticket - best value for 4 people',
                    'from €17.50'
                ),
                new ScheduleCard(
                    '13:00',
                    ['In English', 'In Dutch'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 25',
                    'Group ticket - best value for 4 people',
                    'from €17.50'
                ),
                new ScheduleCard(
                    '16:00',
                    ['In English', 'In Dutch'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 25',
                    'Group ticket - best value for 4 people',
                    'from €17.50'
                ),
            ],
        );

        $friday = new ScheduleDayData(
            dayName: 'Friday',
            fullDate: 'Friday, July 26',
            events: [
                new ScheduleCard(
                    '10:00',
                    ['In English', 'In Dutch'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 26',
                    'Group ticket - best value for 4 people',
                    'from €17.50'
                ),
                new ScheduleCard(
                    '13:00',
                    ['In English', 'In Dutch', 'In Chinese'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 26',
                    'Group ticket - best value for 4 people',
                    'from €17.50'
                ),
                new ScheduleCard(
                    '16:00',
                    ['In English', 'In Dutch'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 26',
                    'Group ticket - best value for 4 people',
                    'from €17.50'
                ),
            ],
        );

        $saturday = new ScheduleDayData(
            dayName: 'Saturday',
            fullDate: 'Saturday, July 27',
            events: [
                new ScheduleCard(
                    '10:00',
                    ['In English', 'In Dutch'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 27',
                    'Group ticket - best value for 4 people',
                    'from €17.50'
                ),
                new ScheduleCard(
                    '13:00',
                    ['In English', 'In Dutch'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 27',
                    'Group ticket - best value for 4 people',
                    'from €17.50'
                ),
                new ScheduleCard(
                    '16:00',
                    ['In English', 'In Dutch', 'In Chinese'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 27',
                    'Group ticket - best value for 4 people',
                    'from €17.50'
                ),
            ],
        );

        $sunday = new ScheduleDayData(
            dayName: 'Sunday',
            fullDate: 'Sunday, July 29',
            events: [
                new ScheduleCard(
                    '10:00',
                    ['In English', 'In Dutch'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 29',
                    'Group ticket - best value for 4 people',
                    'from €17.50'
                ),
                new ScheduleCard(
                    '13:00',
                    ['In English', 'In Dutch'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 29',
                    'Group ticket - best value for 4 people',
                    'from €17.50'
                ),
                new ScheduleCard(
                    '16:00',
                    ['In English', 'In Dutch'],
                    'A giant flag near Church of St. Bavo at Grote Markt',
                    'Thursday, July 29',
                    'Group ticket - best value for 4 people',
                    'from €17.50'
                ),
            ],
        );

        return new ScheduleData(
            headingText: 'Tour schedule',
            filterLabel: 'Filters',
            days: [$thursday, $friday, $saturday, $sunday],
        );
    }

    /**
     * Builds the shared schedule section for storytelling events.
     */
    private function buildScheduleSection(): ScheduleSectionViewModel
    {
        return ScheduleSectionViewModel::fromData(
            $this->scheduleService->getScheduleData('history', EventTypeId::History->value, 7)
        );
    }
}
