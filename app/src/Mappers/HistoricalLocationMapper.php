<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Constants\HistoricalLocationPageConstants;
use App\Models\GlobalUiContent;
use App\Models\HeroSectionContent;
use App\ViewModels\HeroData;
use App\ViewModels\History\HistoricalLocationViewModel;
use App\ViewModels\History\LocationFacts;
use App\ViewModels\History\LocationHero;
use App\ViewModels\History\LocationIntroduction;
use App\ViewModels\History\LocationSignificance;

final class HistoricalLocationMapper
{
    public static function toPageViewModel(
        array $data,
        bool  $isLoggedIn,
    ): HistoricalLocationViewModel
    {
        // $data now comes from HistoricalLocationService::getHistoricalLocationPageData(),
        // which returns ['sections' => [...]]
        $sections = $data['sections'] ?? [];
        $heroData = CmsMapper::toHeroData(
            HeroSectionContent::fromRawArray($sections[HistoricalLocationPageConstants::SECTION_HERO] ?? []),
            'history',
        );
        $locationHero = self::toHeroData($sections[HistoricalLocationPageConstants::SECTION_HERO] ?? []);
        $globalUi = CmsMapper::toGlobalUiData(
            GlobalUiContent::fromRawArray($sections['global_ui'] ?? []),
            $isLoggedIn,
        );

        return new HistoricalLocationViewModel(
            heroData: $heroData,
            globalUi: $globalUi,
            locationHero: $locationHero,
            locationIntroduction: self::toLocationIntroduction($sections['intro_section'] ?? []),
            locationFacts: self::toLocationFacts($sections['facts_section'] ?? []),
            locationSignificance: self::toLocationSignificance($sections['significance_section'] ?? []),
            cms: CmsMapper::toCmsData($heroData, $globalUi),
        );
    }

    private static function toHeroData(array $hero): LocationHero
    {
        return new LocationHero(
            mainTitle: $hero['hero_main_title'] ?? 'A STROLL THROUGH HISTORY',
            subtitle: $hero['hero_subtitle'] ?? 'Explore nine centuries of turbulent history, magnificent architecture, and cultural treasures',
            buttonText: $hero['hero_button'] ?? 'Explore the tour',
            buttonLink: $hero['hero_button_link'] ?? '#route',
            backgroundImageUrl: $hero['hero_background_image'] ?? '/assets/Image/History/History-hero.png',
            currentPage: 'history',
            mapImageUrl: $hero['hero_map_image'] ?? '/assets/Image/History/History-map.png',
        );
    }

    private static function toLocationIntroduction(array $data): LocationIntroduction
    {
        return new LocationIntroduction(
            headingText: $data['intro_heading'] ?? 'Discover the rich history of our city',
            introText: $data['intro_text'] ?? 'Our city has a rich history that spans centuries,',
            factText: $data['intro_fact'] ?? 'Did you know? Our city was founded in the 12th century and has been a hub of culture and commerce ever since.',
            locationImagePath: $data['intro_image'] ?? '',
        );
    }

    private static function toLocationFacts(array $data): LocationFacts
    {
        $facts = [
            $data['fact1'] ?? 'Discover the rich history of our city',
            $data['fact2'] ?? 'Explore the rich history of our city',
            $data['fact3'] ?? 'Uncover the rich history of our city',
        ];
        return new LocationFacts(
            headingText: $data['facts_heading'] ?? 'Discover the rich history of our city',
            facts: $facts,
        );
    }


    private static function toLocationSignificance(array $data): LocationSignificance
    {
        return new LocationSignificance(
            architecturalSignificanceHeadingText: $data['architectural_significance_heading'] ?? 'Architectural Significance',
            architecturalSignificanceText: $data['architectural_significance_text'] ?? 'Our',
            historicalSignificanceHeadingText: $data['historical_significance_heading'] ?? 'Historical Significance',
            historicalSignificanceText: $data['historical_significance_text'] ?? 'Our city',
            locationImagePath: $data['significance_image'] ?? '',
        );
    }
}