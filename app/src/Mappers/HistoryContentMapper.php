<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Content\HistoryRouteSectionContent;
use App\Content\HistoryTicketOptionsSectionContent;
use App\Content\HistoryTourInfoSectionContent;
use App\Content\HistoryVenuesSectionContent;

/**
 * Maps raw CMS arrays into History page content models.
 */
final class HistoryContentMapper
{
    /** Maps raw CMS data to a HistoryRouteSectionContent model. */
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

    /** Maps raw CMS data to a HistoryVenuesSectionContent model. */
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

    /** Maps raw CMS data to a HistoryTicketOptionsSectionContent model. */
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
}
