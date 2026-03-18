<?php

declare(strict_types=1);

namespace App\Mappers;

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
        $heroData = self::toHeroData($data['heroSection']);
        $globalUi = CmsMapper::toGlobalUiData($data['globalUiContent'], $isLoggedIn);

        return new HistoryPageViewModel(
            heroData:           $heroData,
            globalUi:           $globalUi,
            cms:                CmsMapper::toCmsData($heroData, $globalUi),
            gradientSection:    self::toGradientSection($data['gradientSection']),
            introSplitSection:  self::toIntroSplitSection($data['introSection']),
            routeData:          self::toRouteData($data['routeSection']),
            venuesData:         self::toVenuesData($data['venuesSection']),
            ticketOptionsData:  self::toTicketOptions($data['ticketOptionsSection']),
            infoAboutTourData:  self::toInfoAboutTour($data['infoAboutTourSection']),
            scheduleSection:    $scheduleSection,
        );
    }

    private static function toHeroData(array $hero): HeroData
    {
        return new HeroData(
            mainTitle:           $hero['mainTitle'],
            subtitle:            $hero['subtitle'],
            primaryButtonText:   $hero['primaryButtonText'],
            primaryButtonLink:   $hero['primaryButtonLink'],
            secondaryButtonText: $hero['secondaryButtonText'],
            secondaryButtonLink: $hero['secondaryButtonLink'],
            backgroundImageUrl:  $hero['backgroundImageUrl'],
            currentPage:         'history',
        );
    }

    private static function toGradientSection(array $gradient): GradientSectionData
    {
        return new GradientSectionData(
            headingText:        $gradient['headingText'],
            subheadingText:     $gradient['subheadingText'],
            backgroundImageUrl: $gradient['backgroundImageUrl'],
        );
    }

    private static function toIntroSplitSection(array $intro): IntroSplitSectionData
    {
        return new IntroSplitSectionData(
            headingText:  $intro['headingText'],
            bodyText:     $intro['bodyText'],
            imageUrl:     $intro['imageUrl'],
            imageAltText: $intro['imageAltText'],
        );
    }

    private static function toRouteData(array $route): RouteData
    {
        $venues = array_map(
            fn(array $loc) => new RouteVenue(
                venueName:        $loc['venueName'],
                venueBadgeColor:  $loc['venueBadgeColor'],
                venueDescription: $loc['venueDescription'],
            ),
            $route['locations']
        );

        return new RouteData(
            headingText:  $route['headingText'],
            venues:       $venues,
            mapImagePath: $route['mapImagePath'],
        );
    }

    private static function toVenuesData(array $venuesSection): VenuesData
    {
        $venues = array_map(
            fn(array $v) => new VenueCardData(
                name:        $v['name'],
                description: $v['description'],
                imageUrl:    $v['imageUrl'],
            ),
            $venuesSection['venues']
        );

        return new VenuesData(
            headingText: $venuesSection['headingText'],
            venues:      $venues,
        );
    }

    private static function toTicketOptions(array $ticketOptions): TicketOptions
    {
        $cards = array_map(
            fn(array $card) => new PricingCard(
                icon:             $card['icon'],
                title:            $card['title'],
                price:            $card['price'],
                descriptionItems: $card['descriptionItems'],
            ),
            $ticketOptions['pricingCards']
        );

        return new TicketOptions(
            headingText:  $ticketOptions['headingText'],
            pricingCards: $cards,
        );
    }

    private static function toInfoAboutTour(array $info): ImportantInfoAboutTour
    {
        return new ImportantInfoAboutTour(
            headingText: $info['headingText'],
            infoItems:   $info['infoItems'],
        );
    }
}
