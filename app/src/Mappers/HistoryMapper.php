<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Domain\Pages\HistoryPageData;
use App\DTOs\Cms\HistoryRouteSectionContent;
use App\DTOs\Cms\HistoryTicketOptionsSectionContent;
use App\DTOs\Cms\HistoryTourInfoSectionContent;
use App\DTOs\Cms\HistoryVenuesSectionContent;
use App\Constants\HistoryPageConstants;
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
        $heroData = CmsMapper::toHeroData($data->heroSection, HistoryPageConstants::CURRENT_PAGE);
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
            heroData: $heroData, globalUi: $globalUi,
            gradientSection:    CmsMapper::toGradientSection($data->gradientSection),
            introSplitSection:  CmsMapper::toIntroSplitSection($data->introSection),
            routeData:          self::toRouteData($data->routeSection),
            venuesData:         self::toVenuesData($data->venuesSection),
            ticketOptionsData:  self::toTicketOptions($data->ticketOptionsSection),
            infoAboutTourData:  self::toInfoAboutTour($data->tourInfoSection),
            scheduleSection:    $scheduleSection,
        );
    }

    private static function toRouteData(HistoryRouteSectionContent $content): RouteData
    {
        return new RouteData(
            headingText:  $content->routeHeading ?? '',
            venues:       self::buildRouteVenues($content),
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
            headingText:    $content->historicalLocationsHeading ?? '',
            viewMoreLabel:  $content->historicalLocationsViewMoreLabel ?? '',
            venues:         self::buildVenueCards($content),
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

    private static function toTicketOptions(HistoryTicketOptionsSectionContent $content): TicketOptions
    {
        return new TicketOptions(
            headingText:  $content->ticketOptionsHeading ?? '',
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

    private static function toInfoAboutTour(HistoryTourInfoSectionContent $content): ImportantInfoAboutTour
    {
        return new ImportantInfoAboutTour(
            headingText: $content->historyImportantTourInfoHeading ?? '',
            infoItems:   self::buildInfoItems($content),
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
