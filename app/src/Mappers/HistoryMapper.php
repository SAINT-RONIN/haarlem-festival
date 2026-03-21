<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Helpers\AgeLabelFormatter;
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

final class HistoryMapper
{
    public static function toPageViewModel(
        array $data,
        bool $isLoggedIn,
        ?ScheduleSectionViewModel $scheduleSection = null
    ): HistoryPageViewModel {
        // $data now comes from HistoryService::getHistoryPageData(),
        // which returns ['sections' => [...]]
        $sections = $data['sections'] ?? [];

        $heroData = self::toHeroData($sections['hero_section'] ?? []);
        $globalUi = CmsMapper::toGlobalUiData($sections['global_ui'] ?? [], $isLoggedIn);

        return new HistoryPageViewModel(
            heroData:           $heroData,
            globalUi:           $globalUi,
            cms:                CmsMapper::toCmsData($heroData, $globalUi),
            gradientSection:    self::toGradientSection($sections['gradient_section'] ?? []),
            introSplitSection:  self::toIntroSplitSection($sections['intro_section'] ?? []),
            routeData:          self::toRouteData($sections['route_section'] ?? []),
            venuesData:         self::toVenuesData($sections['historical_locations_section'] ?? []),
            ticketOptionsData:  self::toTicketOptions($sections['ticket_options_section'] ?? []),
            infoAboutTourData:  self::toInfoAboutTour($sections['history_important_tour_info_section'] ?? []),
            scheduleSection:    $scheduleSection,
        );
    }

    private static function toHeroData(array $hero): HeroData
    {
        return new HeroData(
            mainTitle:           $hero['hero_main_title'] ?? 'A STROLL THROUGH HISTORY',
            subtitle:            $hero['hero_subtitle'] ?? 'Explore nine centuries of turbulent history, magnificent architecture, and cultural treasures',
            primaryButtonText:   $hero['hero_button_primary'] ?? 'Explore the tour',
            primaryButtonLink:   $hero['hero_button_primary_link'] ?? '#route',
            secondaryButtonText: $hero['hero_button_secondary'] ?? 'Get tickets',
            secondaryButtonLink: $hero['hero_button_secondary_link'] ?? '#tickets',
            backgroundImageUrl:  $hero['hero_background_image'] ?? '/assets/Image/History/History-hero.png',
            currentPage:         'history',
            mapImageUrl:         null
        );
    }

    private static function toGradientSection(array $gradient): GradientSectionData
    {
        return new GradientSectionData(
            headingText:        $gradient['gradient_heading'] ?? 'Every street holds echoes of the past, shaped by the people who once walked there.',
            subheadingText:     $gradient['gradient_subheading'] ?? 'Where history comes alive through places, paths, and people.',
            backgroundImageUrl: $gradient['gradient_background_image'] ?? '/assets/Image/History/History-second-section.png',
        );
    }

    private static function toIntroSplitSection(array $intro): IntroSplitSectionData
    {
        return new IntroSplitSectionData(
            headingText:  $intro['intro_heading'] ?? 'Experience the living history of Haarlem',
            bodyText:     $intro['intro_body'] ?? 'A Stroll Through History invites visitors to explore rich past of Haarlem on foot. Guided tour leads participants through historic streets and landmarks, including locations that played an important role in the city`s cultural, social, and architectural development. The walks are offered in Dutch, English, and Chinese and are suitable for a wide audience. The route has been carefully curated and prepared by local historians and guides to ensure an engaging, informative, and memorable experience. By combining historical facts with stories from the past, the event helps visitors better understand how Haarlem grew into the city it is today. Multiple time slots are available throughout the festival, with different ticket options to keep the event accessible for individuals and families. By joining A Stroll Through History, visitors not only discover Haarlem`s landmarks but also connect with the city through the people, places, and moments that shaped it.',
            imageUrl:     $intro['intro_image'] ?? '/assets/Image/History/History-third-section.png',
            imageAltText: $intro['intro_image_alt'] ?? 'A corner of a historic building in Haarlem',
        );
    }

    private static function toRouteData(array $route): RouteData
    {
        $locations = [
            new RouteVenue(
                venueName: $route['route_location1_name'] ?? 'Church of St.Bavo',
                venueBadgeColor: 'bg-sky-600/80',
                venueDescription: $route['route_location1_description'] ?? 'A monumental Gothic church famed for its towering nave and historic Müller organ once played by Mozart.',
            ),
            new RouteVenue(
                venueName: $route['route_location2_name'] ?? 'Grote Markt',
                venueBadgeColor: 'bg-orange-800/80',
                venueDescription: $route['route_location2_description'] ?? 'A vibrant central square surrounded by landmark buildings and lively cafés;  the city’s cultural heart.',
            ),
            new RouteVenue(
                venueName: $route['route_location3_name'] ?? 'De Hallen',
                venueBadgeColor: 'bg-amber-400/80',
                venueDescription: $route['route_location3_description'] ?? 'A former meat hall turned into an art and photography museum space that hosts exhibitions as part of the Frans Hals Museum.',
            ),
            new RouteVenue(
                venueName: $route['route_location4_name'] ?? 'Proveniershof',
                venueBadgeColor: 'bg-lime-700/80',
                venueDescription: $route['route_location4_description'] ?? 'A peaceful 18th-century hofje (courtyard community) offering a quiet oasis with historic almshouses.',
            ),
            new RouteVenue(
                venueName: $route['route_location5_name'] ?? 'Jopenkerk',
                venueBadgeColor: 'bg-violet-800/80',
                venueDescription: $route['route_location5_description'] ?? 'A former church transformed into Haarlem’s iconic craft brewery and restaurant, blending tradition with modern beer culture.',
            ),
            new RouteVenue(
                venueName: $route['route_location6_name'] ?? 'Waalse Kerk',
                venueBadgeColor: 'bg-rose-500/80',
                venueDescription: $route['route_location6_description'] ?? 'An intimate 17th-century Walloon church known for its serene atmosphere and historic interior.',
            ),
            new RouteVenue(
                venueName: $route['route_location7_name'] ?? 'Molen de Adriaan',
                venueBadgeColor: 'bg-lime-500/80',
                venueDescription: $route['route_location7_description'] ?? 'A reconstructed 18th-century riverside windmill offering tours and panoramic views over the Spaarne.',
            ),
            new RouteVenue(
                venueName: $route['route_location8_name'] ?? 'Amsterdamse Poort',
                venueBadgeColor: 'bg-stone-700/80',
                venueDescription: $route['route_location8_description'] ?? 'Haarlem’s last surviving medieval city gate, showcasing impressive brickwork and centuries of history.',
            ),
            new RouteVenue(
                venueName: $route['route_location9_name'] ?? 'Hof van Bakenes',
                venueBadgeColor: 'bg-orange-500/80',
                venueDescription: $route['route_location9_description'] ?? 'The oldest hofje in the Netherlands, featuring charming gardens and classic courtyard architecture dating back to 1395.',
            ),
            ];

        return new RouteData(
            headingText: $route['route_heading'] ?? 'The Route',
            venues: $locations,
            mapImagePath: $route['route_map_image'] ?? '/assets/Image/History/History-RouteMap.png',
        );
    }

    private static function toVenuesData(array $venuesSection): VenuesData
    {
        // Mirror HistoryService::buildVenuesData
        $venues = [
            new VenueCardData(
                name: $venuesSection['history_grotemarkt_name'] ?? 'Grote Markt',
                description: $venuesSection['history_grotemarkt_description'] ?? 'The heart of the historic center of Haarlem.',
                imageUrl: $venuesSection['history_grotemarkt_image'] ?? '/assets/Image/History/History-GroteMarkt.png',
                venueUrl: $venuesSection['history_grotemarkt_link'] ?? '/history/grote-markt',
            ),
            new VenueCardData(
                name: $venuesSection['history_amsterdamsepoort_name'] ?? 'Amsterdamse Poort',
                description: $venuesSection['history_amsterdamsepoort_description'] ?? 'As the only remaining city gate.',
                imageUrl: $venuesSection['history_amsterdamsepoort_image'] ?? '/assets/Image/History/History-AmsterdamsePoort.png',
                venueUrl: $venuesSection['history_amsterdamsepoort_link'] ?? 'amsterdamse-poort',
            ),
            new VenueCardData(
                name: $venuesSection['history_molendeadriaan_name'] ?? 'Molen De Adriaan',
                description: $venuesSection['history_molendeadriaan_description'] ?? 'A striking riverside windmill.',
                imageUrl: $venuesSection['history_molendeadriaan_image'] ?? '/assets/Image/History/History-MolenDeAdriaan.png',
                venueUrl: $venuesSection['history_molendeadriaan_link'] ?? 'molen-de-adriaan',
            ),
        ];

        return new VenuesData(
            headingText: $venuesSection['historical_locations_heading'] ?? 'Read more about these locations ',
            venues: $venues,
        );
    }

    private static function toTicketOptions(array $ticketOptions): TicketOptions
    {
        // Mirror HistoryService::buildTicketOptionsData
        return new TicketOptions(
            headingText: $ticketOptions['ticket_options_heading'] ?? 'Your ticket options ',
            pricingCards: [
                new PricingCard(
                    icon: $ticketOptions['history_single_ticket_icon'] ?? '/assets/Icons/History/single-ticket-icon.svg',
                    title: $ticketOptions['history_pricing_single_title'] ?? 'Single Ticket',
                    price: $ticketOptions['history_pricing_single_price'] ?? '€17.50',
                    descriptionItems: [
                        $ticketOptions['history_pricing_single_include1'] ?? 'Per person',
                        $ticketOptions['history_pricing_single_include2'] ?? 'Includes one complimentary drink',
                        $ticketOptions['history_pricing_single_include3'] ?? '2.5 hour guided tour',
                    ],
                ),
                new PricingCard(
                    icon: $ticketOptions['history_group_ticket_icon'] ?? '/assets/Icons/History/group-ticket-icon.svg',
                    title: $ticketOptions['history_pricing_group_title'] ?? 'Group Ticket',
                    price: $ticketOptions['history_pricing_group_price'] ?? '€60.00',
                    descriptionItems: [
                        $ticketOptions['history_pricing_group_include1'] ?? 'For up to 4 people',
                        $ticketOptions['history_pricing_group_include2'] ?? 'Includes four complimentary drinks',
                        $ticketOptions['history_pricing_group_include3'] ?? 'Best value for families!',
                    ],
                ),
            ],
        );
    }

    private static function toInfoAboutTour(array $info): ImportantInfoAboutTour
    {
        // Mirror HistoryService::buildInfoAboutTourData
        return new ImportantInfoAboutTour(
            headingText: $info['history_important_tour_info_heading'] ?? 'Important information about the tour',
            infoItems: [
                $info['important_info_item1'] ?? AgeLabelFormatter::formatRequirement(12, null),
                $info['important_info_item2'] ?? 'No strollers allowed due to the nature of the walking route',
                $info['important_info_item3'] ?? 'Tour duration: Approximately 2.5 hours including 15-minute break',
                $info['important_info_item4'] ?? 'Group ticket is the best value for a group of 4 or for a family',
                $info['important_info_item5'] ?? 'Starting point: Look for the giant flag near Church of St. Bavo at Grote Markt',
                $info['important_info_item6'] ?? 'Group size: Maximum 12 participants per guide',
                $info['important_info_item7'] ?? 'Comfortable walking shoes recommended',
                $info['important_info_item8'] ?? 'Tours run in light rain. severe weather cancellations will be communicated via email',
            ],
        );
    }
}
