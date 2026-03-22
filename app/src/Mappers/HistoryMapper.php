<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Models\HistoryGradientSectionContent;
use App\Models\HistoryIntroSectionContent;
use App\Models\HistoryPageData;
use App\Models\HistoryRouteSectionContent;
use App\Models\HistoryTicketOptionsSectionContent;
use App\Models\HistoryTourInfoSectionContent;
use App\Models\HistoryVenuesSectionContent;
use App\Constants\HistoryPageConstants;
use App\ViewModels\GradientSectionData;
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
use App\ViewModels\IntroSplitSectionData;
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
            heroData:           $heroData,
            globalUi:           $globalUi,
            cms:                CmsMapper::toCmsData($heroData, $globalUi),
            gradientSection:    self::toGradientSection($data->gradientSection),
            introSplitSection:  self::toIntroSplitSection($data->introSection),
            routeData:          self::toRouteData($data->routeSection),
            venuesData:         self::toVenuesData($data->venuesSection),
            ticketOptionsData:  self::toTicketOptions($data->ticketOptionsSection),
            infoAboutTourData:  self::toInfoAboutTour($data->tourInfoSection),
            scheduleSection:    $scheduleSection,
        );
    }

    private static function toGradientSection(HistoryGradientSectionContent $content): GradientSectionData
    {
        return new GradientSectionData(
            headingText:        $content->gradientHeading ?? '',
            subheadingText:     $content->gradientSubheading ?? '',
            backgroundImageUrl: $content->gradientBackgroundImage ?? '',
        );
    }

    private static function toIntroSplitSection(HistoryIntroSectionContent $content): IntroSplitSectionData
    {
        return new IntroSplitSectionData(
            headingText:  $content->introHeading ?? '',
            bodyText:     $content->introBody ?? '',
            imageUrl:     $content->introImage ?? '',
            imageAltText: $content->introImageAlt ?? '',
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

    /** @return RouteVenue[] */
    private static function buildRouteVenues(HistoryRouteSectionContent $content): array
    {
        return [
            new RouteVenue(venueName: $content->routeLocation1Name ?? '', venueBadgeColor: 'bg-sky-600/80',    venueDescription: $content->routeLocation1Description ?? ''),
            new RouteVenue(venueName: $content->routeLocation2Name ?? '', venueBadgeColor: 'bg-orange-800/80', venueDescription: $content->routeLocation2Description ?? ''),
            new RouteVenue(venueName: $content->routeLocation3Name ?? '', venueBadgeColor: 'bg-amber-400/80',  venueDescription: $content->routeLocation3Description ?? ''),
            new RouteVenue(venueName: $content->routeLocation4Name ?? '', venueBadgeColor: 'bg-lime-700/80',   venueDescription: $content->routeLocation4Description ?? ''),
            new RouteVenue(venueName: $content->routeLocation5Name ?? '', venueBadgeColor: 'bg-violet-800/80', venueDescription: $content->routeLocation5Description ?? ''),
            new RouteVenue(venueName: $content->routeLocation6Name ?? '', venueBadgeColor: 'bg-rose-500/80',   venueDescription: $content->routeLocation6Description ?? ''),
            new RouteVenue(venueName: $content->routeLocation7Name ?? '', venueBadgeColor: 'bg-lime-500/80',   venueDescription: $content->routeLocation7Description ?? ''),
            new RouteVenue(venueName: $content->routeLocation8Name ?? '', venueBadgeColor: 'bg-stone-700/80',  venueDescription: $content->routeLocation8Description ?? ''),
            new RouteVenue(venueName: $content->routeLocation9Name ?? '', venueBadgeColor: 'bg-orange-500/80', venueDescription: $content->routeLocation9Description ?? ''),
        ];
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
