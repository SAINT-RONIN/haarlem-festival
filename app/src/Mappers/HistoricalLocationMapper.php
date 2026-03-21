<?php

declare(strict_types=1);

namespace App\Mappers;

use App\ViewModels\HeroData;
use App\ViewModels\History\HistoricalLocationViewModel;
use App\ViewModels\History\LocationFacts;
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

        $heroData = self::toHeroData($sections['hero_section'] ?? []);
        $globalUi = CmsMapper::toGlobalUiData($sections['global_ui'] ?? [], $isLoggedIn);

        return new HistoricalLocationViewModel(
            heroData: $heroData,
            locationIntroduction: self::toLocationIntroduction($sections['intro_section'] ?? []),
            locationFacts: self::toLocationFacts($sections['facts_section'] ?? []),
            locationSignificance: self::toLocationSignificance($sections['significance_section'] ?? []),
            globalUi: $globalUi,
            cms: CmsMapper::toCmsData($heroData, $globalUi),
        );
    }

    private static function toHeroData(array $hero): HeroData
    {
        return new HeroData(
            mainTitle: $hero['hero_main_title'] ?? 'A STROLL THROUGH HISTORY',
            subtitle: $hero['hero_subtitle'] ?? 'Explore nine centuries of turbulent history, magnificent architecture, and cultural treasures',
            primaryButtonText: $hero['hero_button'] ?? 'Explore the tour',
            primaryButtonLink: $hero['hero_button_link'] ?? '#route',
            secondaryButtonText: null,
            secondaryButtonLink: null,
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
            headingText: $data['facts_title'] ?? 'Discover the rich history of our city',
            facts: $facts,
        );
    }

    private static function toLocationSignificance(array $data): LocationSignificance
    {
        return new LocationSignificance(
            architecturalSignificanceHeadingText: $data['significance_architectural_heading'] ?? 'Architectural Significance',
            architecturalSignificanceText: $data['significance_architectural_text'] ?? 'Our',
            historicalSignificanceHeadingText: $data['significance_historical_heading'] ?? 'Historical Significance',
            historicalSignificanceText: $data['significance_historical_text'] ?? 'Our city',
            locationImagePath: $data['significance_location_image_path'] ?? '',
        );
    }
}