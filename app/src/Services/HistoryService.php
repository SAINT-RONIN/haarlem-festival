<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EventTypeId;
use App\Models\CmsItem;
use App\Models\CmsSection;
use App\Mappers\CmsMapper;
use App\Mappers\ScheduleMapper;
use App\Repositories\Interfaces\ICmsRepository;
use App\Services\Interfaces\IHistoryService;
use App\Services\Interfaces\IScheduleService;
use App\Services\Interfaces\ICmsPageContentService;
use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\History\HistoryPageViewModel;
use App\ViewModels\History\ImportantInfoAboutTour;
use App\ViewModels\History\PricingCard;
use App\ViewModels\History\RouteData;
use App\ViewModels\History\RouteVenue;
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
    private ?int $historyPageId = null;
    /** @var array<string, CmsSection>|null */
    private ?array $historySections = null;
    /** @var array<string, list<CmsItem>>|null */
    private ?array $historyItemsBySection = null;

    public function __construct(
        private ICmsRepository $cmsRepository,
        private ICmsPageContentService $cmsService,
        private IScheduleService $scheduleService,
    ) {
    }

    /**
     * Builds the history page view model with all required data.
     */
    public function getHistoryPageData(bool $isLoggedIn): HistoryPageViewModel
    {
        // Load page and sections once
        $this->loadPageData('history');

        $heroData = $this->buildHeroData();
        $globalUi = $this->buildGlobalUi($isLoggedIn);

        return new HistoryPageViewModel(
            heroData: $heroData,
            globalUi: $globalUi,
            cms: CmsMapper::toCmsData($heroData, $globalUi),
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
     * Loads and caches CMS page and sections for the history page.
     */
    private function loadPageData(string $pageName): void
    {
        if ($this->historyPageId !== null) {
            return;
        }

        $pages = $this->cmsRepository->findPages(['slug' => $pageName]);
        if ($pages === []) {
            return;
        }

        $this->historyPageId = $pages[0]->cmsPageId;
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
     * Gets a CMS-managed image URL for the History page.
     *
     * Supports both MEDIA items (MediaAssetId) and legacy IMAGE_PATH items (TextValue).
     *
     * @param string $sectionKey
     * @param string $itemKey
     * @param string $defaultUrl
     * @return string
     */

    /**
     * Builds hero section data for the history page.
     */
    private function buildHeroData(): HeroData
    {
        $sectionData = $this->cmsService->getSectionContent('history', 'hero_section');
        return new HeroData(
            mainTitle: $sectionData['hero_main_title'] ?? 'A STROLL THROUGH HISTORY',
            subtitle: $sectionData['hero_subtitle'] ?? 'Explore nine centuries of turbulent history, magnificent architecture, and cultural treasures',
            primaryButtonText: $sectionData['hero_button_primary'] ?? 'Explore the tour',
            primaryButtonLink: $sectionData['hero_button_primary_link'] ?? '#route',
            secondaryButtonText: $sectionData['hero_button_secondary'] ?? 'Get tickets',
            secondaryButtonLink: $sectionData['hero_button_secondary_link'] ??  '#tickets',
            backgroundImageUrl: '/assets/Image/History/History-hero.png',
            currentPage: 'history',
        );
    }

    /**
     * Builds global UI navigation and button labels.
     */
    private function buildGlobalUi(bool $isLoggedIn): GlobalUiData
    {
        $globalUiContent = $this->cmsService->getSectionContent('home', 'global_ui');

        return CmsMapper::toGlobalUiData($globalUiContent, $isLoggedIn);
    }


    /**
     * Builds the gradient overlay section content.
     */
    private function buildGradientSection(): GradientSectionData
    {
        $sectionData = $this->cmsService->getSectionContent('history', 'gradient_section');

        return new GradientSectionData(
            headingText: $sectionData['gradient_heading'] ?? 'Every street holds echoes of the past, shaped by the people who once walked there.',
            subheadingText: $sectionData['gradient_subheading'] ?? 'Where history comes alive through places, paths, and people.',
            backgroundImageUrl: $sectionData['gradient_background_image'] ?? '/assets/Image/History/History-second-section.png',
        );
    }


    /**
     * Builds the split intro section with text and supporting image.
     */
    private function buildIntroSplitSection(): IntroSplitSectionData
    {
        // retrieve intro section content
        $sectionData = $this->cmsService->getSectionContent('history', 'intro_section');

        return new IntroSplitSectionData(
            headingText: $sectionData['intro_heading'] ?? 'Experience the living history of Haarlem',
            bodyText: $sectionData['intro_body'] ?? 'A Stroll Through History invites visitors to explore rich past of Haarlem on foot. Guided tour leads participants through historic streets and landmarks, including locations that played an important role in the city`s cultural, social, and architectural development. The walks are offered in Dutch, English, and Chinese and are suitable for a wide audience. The route has been carefully curated and prepared by local historians and guides to ensure an engaging, informative, and memorable experience. By combining historical facts with stories from the past, the event helps visitors better understand how Haarlem grew into the city it is today. Multiple time slots are available throughout the festival, with different ticket options to keep the event accessible for individuals and families. By joining A Stroll Through History, visitors not only discover Haarlem`s landmarks but also connect with the city through the people, places, and moments that shaped it.',
            imageUrl: $sectionData['intro_image'] ?? '/assets/Image/History/History-third-section.png',
            imageAltText: $sectionData['intro_image_alt'] ?? 'A corner of a historic building in Haarlem',
        );
    }

    /**
     * Builds the route data for the historical walking tour.
     *
     * @return RouteData Contains route heading, ordered venues and map image.
     */
    private function buildRouteData(): RouteData
    {
        // retrieve route section content
        $sectionData = $this->cmsService->getSectionContent('history', 'route_section');
        $locations = [
            new RouteVenue(
                venueName: $sectionData['route_location1_name'] ?? 'Church of St.Bavo',
                venueBadgeColor: 'bg-sky-600/80',
                venueDescription: $sectionData['route_location1_description'] ?? 'A monumental Gothic church famed for its towering nave and historic Müller organ once played by Mozart.',
            ),
            new RouteVenue(
                venueName: $sectionData['route_location2_name'] ?? 'Grote Markt',
                venueBadgeColor: 'bg-orange-800/80',
                venueDescription: $sectionData['route_location2_description'] ?? 'A vibrant central square surrounded by landmark buildings and lively cafés;  the city’s cultural heart.',
            ),
            new RouteVenue(
                venueName: $sectionData['route_location3_name'] ?? 'De Hallen',
                venueBadgeColor: 'bg-amber-400/80',
                venueDescription: $sectionData['route_location3_description'] ?? 'A former meat hall turned into an art and photography museum space that hosts exhibitions as part of the Frans Hals Museum.',
            ),
            new RouteVenue(
                venueName: $sectionData['route_location4_name'] ?? 'Proveniershof',
                venueBadgeColor: 'bg-lime-700/80',
                venueDescription: $sectionData['route_location4_description'] ?? 'A peaceful 18th-century hofje (courtyard community) offering a quiet oasis with historic almshouses.',
            ),
            new RouteVenue(
                venueName: $sectionData['route_location5_name'] ?? 'Jopenkerk',
                venueBadgeColor: 'bg-violet-800/80',
                venueDescription: $sectionData['route_location5_description'] ?? 'A former church transformed into Haarlem’s iconic craft brewery and restaurant, blending tradition with modern beer culture.',
            ),
            new RouteVenue(
                venueName: $sectionData['route_location6_name'] ?? 'Waalse Kerk',
                venueBadgeColor: 'bg-rose-500/80',
                venueDescription: $sectionData['route_location6_description'] ?? 'An intimate 17th-century Walloon church known for its serene atmosphere and historic interior.',
            ),
            new RouteVenue(
                venueName: $sectionData['route_location7_name'] ?? 'Molen de Adriaan',
                venueBadgeColor: 'bg-lime-500/80',
                venueDescription: $sectionData['route_location7_description'] ?? 'A reconstructed 18th-century riverside windmill offering tours and panoramic views over the Spaarne.',
            ),
            new RouteVenue(
                venueName: $sectionData['route_location8_name'] ?? 'Amsterdamse Poort',
                venueBadgeColor: 'bg-stone-700/80',
                venueDescription: $sectionData['route_location8_description'] ?? 'Haarlem’s last surviving medieval city gate, showcasing impressive brickwork and centuries of history.',
            ),
            new RouteVenue(
                venueName: $sectionData['route_location9_name'] ?? 'Hof van Bakenes',
                venueBadgeColor: 'bg-orange-500/80',
                venueDescription: $sectionData['route_location9_description'] ?? 'The oldest hofje in the Netherlands, featuring charming gardens and classic courtyard architecture dating back to 1395.',
            ),
        ];

        return new RouteData(
            headingText: $sectionData['route_heading'] ?? 'The Route',
            venues: $locations,
            mapImagePath: $sectionData['route_map_image'] ?? '/assets/Image/History/History-RouteMap.png'
        );
    }

    /**
     * Builds the "Read more about these locations" venues section.
     *
     * @return VenuesData Card data for a curated subset of route venues.
     */
    private function buildVenuesData(): VenuesData
    {
        // retrieve venues section content
        $sectionData = $this->cmsService->getSectionContent('history', 'historical_locations_section');
        $venues = [
            new VenueCardData(
                name: $sectionData['history_grotemarkt_name'] ?? 'Grote Markt',
                description: $sectionData['history_grotemarkt_description'] ?? 'The heart of the historic center of Haarlem.',
                imageUrl: $sectionData['history_grotemarkt_image'] ?? '/assets/Image/History/History-GroteMarkt.png',
            ),
            new VenueCardData(
                name: $sectionData['history_amsterdamsepoort_name'] ?? 'Amsterdamse Poort',
                description: $sectionData['history_amsterdamsepoort_description'] ?? 'As the only remaining city gate.',
                imageUrl: $sectionData['history_amsterdamsepoort_image'] ?? '/assets/Image/History/History-AmsterdamsePoort.png',
            ),
            new VenueCardData(
                name: $sectionData['history_molendeadriaan_name'] ?? 'Molen De Adriaan',
                description: $sectionData['history_molendeadriaan_description'] ?? 'A striking riverside windmill.',
                imageUrl: $sectionData['history_molendeadriaan_image'] ?? '/assets/Image/History/History-MolenDeAdriaan.png',
            ),
        ];

        return new VenuesData(
            headingText: $sectionData['historical_locations_heading'] ?? 'Read more about these locations ',
            venues: $venues,
        );
    }

    /**
     * Builds ticket options (single and group pricing cards) for the tour.
     */
    private function buildTicketOptionsData(): TicketOptions
    {
        // retrieve tickets section content
        $sectionData = $this->cmsService->getSectionContent('history', 'ticket_options_section');
        return new TicketOptions(
            headingText: $sectionData['ticket_options_heading'] ?? 'Your ticket options ',
            pricingCards: [
                new PricingCard(
                    icon: $sectionData['history_single_ticket_icon'] ?? '/assets/Icons/History/single-ticket-icon.svg',
                    title: $sectionData['history_pricing_single_title'] ??  'Single Ticket',
                    price: $sectionData['history_pricing_single_price'] ?? '€17.50',
                    descriptionItems: [
                        $sectionData['history_pricing_single_include1'] ??  'Per person',
                        $sectionData['history_pricing_single_include2'] ?? 'Includes one complimentary drink',
                        $sectionData['history_pricing_single_include3'] ?? '2.5 hour guided tour',
                    ]
                ),
                new PricingCard(
                    icon: $sectionData['history_group_ticket_icon'] ?? '/assets/Icons/History/group-ticket-icon.svg',
                    title: $sectionData['history_pricing_group_title'] ??  'Group Ticket',
                    price: $sectionData['history_pricing_group_price'] ?? '€60.00',
                    descriptionItems: [
                        $sectionData['history_pricing_group_include1'] ?? 'For up to 4 people',
                        $sectionData['history_pricing_group_include2'] ?? 'Includes four complimentary drinks',
                        $sectionData['history_pricing_group_include3'] ?? 'Best value for families!',
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
        // retrieve info about tour section content
        $sectionData = $this->cmsService->getSectionContent('history', 'history_important_tour_info_section');
        return new ImportantInfoAboutTour(
            headingText: $sectionData['history_important_tour_info_heading'] ?? 'Important information about the tour',
            infoItems: [
                $sectionData['important_info_item1'] ?? AgeLabelFormatter::formatRequirement(12, null),
                $sectionData['important_info_item2'] ?? 'No strollers allowed due to the nature of the walking route',
                $sectionData['important_info_item3'] ?? 'Tour duration: Approximately 2.5 hours including 15-minute break',
                $sectionData['important_info_item4'] ?? 'Group ticket is the best value for a group of 4 or for a family',
                $sectionData['important_info_item5'] ?? 'Starting point: Look for the giant flag near Church of St. Bavo at Grote Markt',
                $sectionData['important_info_item6'] ?? 'Group size: Maximum 12 participants per guide',
                $sectionData['important_info_item7'] ?? 'Comfortable walking shoes recommended',
                $sectionData['important_info_item8'] ?? 'Tours run in light rain; severe weather cancellations will be communicated via email',
            ],
        );
    }

    /**
     * Builds the shared schedule section for storytelling events.
     */
    private function buildScheduleSection(): ScheduleSectionViewModel
    {
        return ScheduleMapper::toScheduleSection(
            $this->scheduleService->getScheduleData('history', EventTypeId::History->value, 7)
        );
    }
}
