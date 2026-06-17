<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Domain\Pages\HistoryPageData;
use App\DTOs\Cms\HistoryRouteSectionContent;
use App\DTOs\Cms\HistoryTicketOptionsSectionContent;
use App\DTOs\Cms\HistoryTourInfoSectionContent;
use App\DTOs\Cms\HistoryVenuesSectionContent;
use App\ViewModels\GlobalUiData;
use App\ViewModels\HeroData;
use App\ViewModels\History\HistoryPageViewModel;
use App\ViewModels\History\ImportantInfoAboutTour;
use App\ViewModels\History\PricingCard;
use App\ViewModels\History\RouteData;
use App\ViewModels\History\RouteVenue;
use App\ViewModels\History\TicketOptions;
use App\ViewModels\History\VenueCardData;
use App\ViewModels\History\VenuesData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

final class HistoryMapper
{
    public static function toPageViewModel(
        HistoryPageData $data,
        bool $isLoggedIn,
        ?ScheduleSectionViewModel $scheduleSection = null
    ): HistoryPageViewModel {
        $heroData = CmsMapper::toHeroData($data->heroSection, 'history');
        $globalUi = CmsMapper::toGlobalUiData($data->globalUiContent, $isLoggedIn);

        return self::buildPageViewModel($data, $heroData, $globalUi, $scheduleSection);
    }

    private static function buildPageViewModel(
        HistoryPageData $data,
        HeroData $heroData,
        GlobalUiData $globalUi,
        ?ScheduleSectionViewModel $scheduleSection,
    ): HistoryPageViewModel {
        return new HistoryPageViewModel(
            heroData: $heroData,
            globalUi: $globalUi,
            gradientSection: CmsMapper::toGradientSection($data->gradientSection),
            introSplitSection: CmsMapper::toIntroSplitSection($data->introSection),
            routeData: self::toRouteData($data->routeSection),
            venuesData: self::toVenuesData($data->venuesSection),
            ticketOptionsData: self::toTicketOptions($data->ticketOptionsSection),
            infoAboutTourData: self::toInfoAboutTour($data->tourInfoSection),
            scheduleSection: $scheduleSection,
        );
    }

    //Maps raw CMS data to a HistoryRouteSectionContent model
    public static function mapRoute(array $raw): HistoryRouteSectionContent
    {
        return new HistoryRouteSectionContent(
            routeHeading: $raw['route_heading'] ?? null,
            routeMapImage: $raw['route_map_image'] ?? null,
            routeLocation1Name: $raw['route_location1_name'] ?? null,
            routeLocation1Description: $raw['route_location1_description'] ?? null,
            routeLocation2Name: $raw['route_location2_name'] ?? null,
            routeLocation2Description: $raw['route_location2_description'] ?? null,
            routeLocation3Name: $raw['route_location3_name'] ?? null,
            routeLocation3Description: $raw['route_location3_description'] ?? null,
            routeLocation4Name: $raw['route_location4_name'] ?? null,
            routeLocation4Description: $raw['route_location4_description'] ?? null,
            routeLocation5Name: $raw['route_location5_name'] ?? null,
            routeLocation5Description: $raw['route_location5_description'] ?? null,
            routeLocation6Name: $raw['route_location6_name'] ?? null,
            routeLocation6Description: $raw['route_location6_description'] ?? null,
            routeLocation7Name: $raw['route_location7_name'] ?? null,
            routeLocation7Description: $raw['route_location7_description'] ?? null,
            routeLocation8Name: $raw['route_location8_name'] ?? null,
            routeLocation8Description: $raw['route_location8_description'] ?? null,
            routeLocation9Name: $raw['route_location9_name'] ?? null,
            routeLocation9Description: $raw['route_location9_description'] ?? null,
        );
    }

    //Maps raw CMS data to a HistoryVenuesSectionContent model.
    public static function mapVenues(array $raw): HistoryVenuesSectionContent
    {
        return new HistoryVenuesSectionContent(
            historicalLocationsHeading: $raw['historical_locations_heading'] ?? null,
            historicalLocationsViewMoreLabel: $raw['historical_locations_view_more_label'] ?? null,
            historyGrotemarktName: $raw['history_grotemarkt_name'] ?? null,
            historyGrotemarktDescription: $raw['history_grotemarkt_description'] ?? null,
            historyGrotemarktImage: $raw['history_grotemarkt_image'] ?? null,
            historyGrotemarktLink: $raw['history_grotemarkt_link'] ?? null,
            historyAmsterdamsepoortName: $raw['history_amsterdamsepoort_name'] ?? null,
            historyAmsterdamsepoortDescription: $raw['history_amsterdamsepoort_description'] ?? null,
            historyAmsterdamsepoortImage: $raw['history_amsterdamsepoort_image'] ?? null,
            historyAmsterdamsepoortLink: $raw['history_amsterdamsepoort_link'] ?? null,
            historyMolendeadriaanName: $raw['history_molendeadriaan_name'] ?? null,
            historyMolendeadriaanDescription: $raw['history_molendeadriaan_description'] ?? null,
            historyMolendeadriaanImage: $raw['history_molendeadriaan_image'] ?? null,
            historyMolendeadriaanLink: $raw['history_molendeadriaan_link'] ?? null,
        );
    }

    private static function toRouteData(HistoryRouteSectionContent $content): RouteData
    {
        return new RouteData(
            headingText: $content->routeHeading ?? '',
            venues: self::buildRouteVenues($content),
            mapImagePath: $content->routeMapImage ?? '',
        );
    }

    private const ROUTE_BADGE_COLORS = [
        'bg-sky-600/80', 'bg-orange-800/80', 'bg-amber-400/80', 'bg-lime-700/80', 'bg-violet-800/80',
        'bg-rose-500/80', 'bg-lime-500/80', 'bg-stone-700/80', 'bg-orange-500/80',
    ];

    /** @return RouteVenue[] */
    private static function buildRouteVenues(HistoryRouteSectionContent $c): array
    {
        $names = [$c->routeLocation1Name, $c->routeLocation2Name, $c->routeLocation3Name, $c->routeLocation4Name, $c->routeLocation5Name, $c->routeLocation6Name, $c->routeLocation7Name, $c->routeLocation8Name, $c->routeLocation9Name];
        $descs = [$c->routeLocation1Description, $c->routeLocation2Description, $c->routeLocation3Description, $c->routeLocation4Description, $c->routeLocation5Description, $c->routeLocation6Description, $c->routeLocation7Description, $c->routeLocation8Description, $c->routeLocation9Description];

        return array_map(
            fn(int $i) => new RouteVenue(venueName: $names[$i] ?? '', venueBadgeColor: self::ROUTE_BADGE_COLORS[$i], venueDescription: $descs[$i] ?? ''),
            range(0, 8),
        );
    }

    private static function toVenuesData(HistoryVenuesSectionContent $content): VenuesData
    {
        return new VenuesData(
            headingText: $content->historicalLocationsHeading ?? '',
            viewMoreLabel: $content->historicalLocationsViewMoreLabel ?? '',
            venues: self::buildVenueCards($content),
        );
    }

    /** @return VenueCardData[] */
    private static function buildVenueCards(HistoryVenuesSectionContent $c): array
    {
        return [
            self::buildVenueCard($c->historyGrotemarktName, $c->historyGrotemarktDescription, $c->historyGrotemarktImage, $c->historyGrotemarktLink),
            self::buildVenueCard($c->historyAmsterdamsepoortName, $c->historyAmsterdamsepoortDescription, $c->historyAmsterdamsepoortImage, $c->historyAmsterdamsepoortLink),
            self::buildVenueCard($c->historyMolendeadriaanName, $c->historyMolendeadriaanDescription, $c->historyMolendeadriaanImage, $c->historyMolendeadriaanLink),
        ];
    }

    private static function buildVenueCard(?string $name, ?string $description, ?string $imageUrl, ?string $venueUrl): VenueCardData
    {
        return new VenueCardData(
            name: $name ?? '',
            description: $description ?? '',
            imageUrl: $imageUrl ?? '',
            venueUrl: $venueUrl ?? '',
        );
    }

    //Maps raw CMS data to a HistoryTicketOptionsSectionContent model
    public static function mapTicketOptions(array $raw): HistoryTicketOptionsSectionContent
    {
        return new HistoryTicketOptionsSectionContent(
            ticketOptionsHeading: $raw['ticket_options_heading'] ?? null,
            historySingleTicketIcon: $raw['history_single_ticket_icon'] ?? null,
            historyPricingSingleTitle: $raw['history_pricing_single_title'] ?? null,
            historyPricingSinglePrice: $raw['history_pricing_single_price'] ?? null,
            historyPricingSingleInclude1: $raw['history_pricing_single_include1'] ?? null,
            historyPricingSingleInclude2: $raw['history_pricing_single_include2'] ?? null,
            historyPricingSingleInclude3: $raw['history_pricing_single_include3'] ?? null,
            historyGroupTicketIcon: $raw['history_group_ticket_icon'] ?? null,
            historyPricingGroupTitle: $raw['history_pricing_group_title'] ?? null,
            historyPricingGroupPrice: $raw['history_pricing_group_price'] ?? null,
            historyPricingGroupInclude1: $raw['history_pricing_group_include1'] ?? null,
            historyPricingGroupInclude2: $raw['history_pricing_group_include2'] ?? null,
            historyPricingGroupInclude3: $raw['history_pricing_group_include3'] ?? null,
        );
    }

    private static function toTicketOptions(HistoryTicketOptionsSectionContent $content): TicketOptions
    {
        return new TicketOptions(
            headingText: $content->ticketOptionsHeading ?? '',
            pricingCards: self::buildPricingCards($content),
        );
    }

    /** @return PricingCard[] */
    private static function buildPricingCards(HistoryTicketOptionsSectionContent $c): array
    {
        return [
            self::buildPricingCard($c->historySingleTicketIcon, $c->historyPricingSingleTitle, $c->historyPricingSinglePrice, [$c->historyPricingSingleInclude1, $c->historyPricingSingleInclude2, $c->historyPricingSingleInclude3]),
            self::buildPricingCard($c->historyGroupTicketIcon, $c->historyPricingGroupTitle, $c->historyPricingGroupPrice, [$c->historyPricingGroupInclude1, $c->historyPricingGroupInclude2, $c->historyPricingGroupInclude3]),
        ];
    }

    /** @param array<int, ?string> $includes */
    private static function buildPricingCard(?string $icon, ?string $title, ?string $price, array $includes): PricingCard
    {
        return new PricingCard(
            icon: $icon ?? '',
            title: $title ?? '',
            price: $price ?? '',
            descriptionItems: array_map(fn(?string $s) => $s ?? '', $includes),
        );
    }

    /** Maps raw CMS data to a HistoryTourInfoSectionContent model. */
    public static function mapTourInfo(array $raw): HistoryTourInfoSectionContent
    {
        return new HistoryTourInfoSectionContent(
            historyImportantTourInfoHeading: $raw['history_important_tour_info_heading'] ?? null,
            importantInfoItem1: $raw['important_info_item1'] ?? null,
            importantInfoItem2: $raw['important_info_item2'] ?? null,
            importantInfoItem3: $raw['important_info_item3'] ?? null,
            importantInfoItem4: $raw['important_info_item4'] ?? null,
            importantInfoItem5: $raw['important_info_item5'] ?? null,
            importantInfoItem6: $raw['important_info_item6'] ?? null,
            importantInfoItem7: $raw['important_info_item7'] ?? null,
            importantInfoItem8: $raw['important_info_item8'] ?? null,
        );
    }

    private static function toInfoAboutTour(HistoryTourInfoSectionContent $content): ImportantInfoAboutTour
    {
        return new ImportantInfoAboutTour(
            headingText: $content->historyImportantTourInfoHeading ?? '',
            infoItems: self::buildInfoItems($content),
        );
    }

    /** @return string[] */
    private static function buildInfoItems(HistoryTourInfoSectionContent $content): array
    {
        return [
            $content->importantInfoItem1 ?? '',
            $content->importantInfoItem2 ?? '',
            $content->importantInfoItem3 ?? '',
            $content->importantInfoItem4 ?? '',
            $content->importantInfoItem5 ?? '',
            $content->importantInfoItem6 ?? '',
            $content->importantInfoItem7 ?? '',
            $content->importantInfoItem8 ?? '',
        ];
    }
}
