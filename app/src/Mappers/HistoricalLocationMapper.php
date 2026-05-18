<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Cms\HistoricalLocationFactsContent;
use App\DTOs\Cms\HistoricalLocationHeroContent;
use App\DTOs\Cms\HistoricalLocationIntroContent;
use App\DTOs\Domain\Pages\HistoricalLocationPageData;
use App\DTOs\Cms\HistoricalLocationSignificanceContent;
use App\ViewModels\GlobalUiData;
use App\ViewModels\HeroData;
use App\ViewModels\History\HistoricalLocationViewModel;
use App\ViewModels\History\LocationFacts;
use App\ViewModels\History\LocationHero;
use App\ViewModels\History\LocationIntroduction;
use App\ViewModels\History\LocationSignificance;

final class HistoricalLocationMapper
{
    public static function toPageViewModel(
        HistoricalLocationPageData $data,
        bool $isLoggedIn,
    ): HistoricalLocationViewModel {
        $heroData = CmsMapper::toHeroData($data->heroSection, 'history');
        $locationHero = self::toLocationHero($data->locationHeroSection);
        $globalUi = CmsMapper::toGlobalUiData($data->globalUiContent, $isLoggedIn);

        return self::buildViewModel($heroData, $globalUi, $locationHero, $data);
    }

    private static function buildViewModel(
        HeroData $heroData,
        GlobalUiData $globalUi,
        LocationHero $locationHero,
        HistoricalLocationPageData $data,
    ): HistoricalLocationViewModel {
        return new HistoricalLocationViewModel(
            heroData: $heroData,
            globalUi: $globalUi,
            locationHero: $locationHero,
            locationIntroduction: self::toLocationIntroduction($data->introSection),
            locationFacts: self::toLocationFacts($data->factsSection),
            locationSignificance: self::toLocationSignificance($data->significanceSection),
        );
    }

    private static function toLocationHero(HistoricalLocationHeroContent $content): LocationHero
    {
        return new LocationHero(
            mainTitle: $content->heroMainTitle ?? '',
            subtitle: $content->heroSubtitle ?? '',
            buttonText: $content->heroButton ?? '',
            buttonLink: $content->heroButtonLink ?? '',
            backgroundImageUrl: $content->heroBackgroundImage ?? '',
            currentPage: 'history',
            mapImageUrl: $content->heroMapImage ?? '',
        );
    }

    private static function toLocationIntroduction(HistoricalLocationIntroContent $content): LocationIntroduction
    {
        return new LocationIntroduction(
            headingText: $content->introHeading ?? '',
            introText: $content->introText ?? '',
            factText: $content->introFact ?? '',
            locationImagePath: $content->introImage ?? '',
        );
    }

    private static function toLocationFacts(HistoricalLocationFactsContent $content): LocationFacts
    {
        return new LocationFacts(
            headingText: $content->factsHeading ?? '',
            facts: [
                $content->fact1 ?? '',
                $content->fact2 ?? '',
                $content->fact3 ?? '',
            ],
        );
    }

    private static function toLocationSignificance(HistoricalLocationSignificanceContent $content): LocationSignificance
    {
        return new LocationSignificance(
            architecturalSignificanceHeadingText: $content->architecturalSignificanceHeading ?? '',
            architecturalSignificanceText: $content->architecturalSignificanceText ?? '',
            historicalSignificanceHeadingText: $content->historicalSignificanceHeading ?? '',
            historicalSignificanceText: $content->historicalSignificanceText ?? '',
            locationImagePath: $content->significanceImage ?? '',
        );
    }

    /** Maps raw CMS data to a HistoricalLocationHeroContent model. */
    public static function mapHero(array $raw): HistoricalLocationHeroContent
    {
        return new HistoricalLocationHeroContent(
            heroMainTitle: $raw['hero_main_title'] ?? null,
            heroSubtitle: $raw['hero_subtitle'] ?? null,
            heroButton: $raw['hero_button'] ?? null,
            heroButtonLink: $raw['hero_button_link'] ?? null,
            heroBackgroundImage: $raw['hero_background_image'] ?? null,
            heroMapImage: $raw['hero_map_image'] ?? null,
        );
    }

    /** Maps raw CMS data to a HistoricalLocationIntroContent model. */
    public static function mapIntro(array $raw): HistoricalLocationIntroContent
    {
        return new HistoricalLocationIntroContent(
            introHeading: $raw['intro_heading'] ?? null,
            introText: $raw['intro_text'] ?? null,
            introFact: $raw['intro_fact'] ?? null,
            introImage: $raw['intro_image'] ?? null,
        );
    }

    /** Maps raw CMS data to a HistoricalLocationFactsContent model. */
    public static function mapFacts(array $raw): HistoricalLocationFactsContent
    {
        return new HistoricalLocationFactsContent(
            factsHeading: $raw['facts_heading'] ?? null,
            fact1: $raw['fact1'] ?? null,
            fact2: $raw['fact2'] ?? null,
            fact3: $raw['fact3'] ?? null,
        );
    }

    /** Maps raw CMS data to a HistoricalLocationSignificanceContent model. */
    public static function mapSignificance(array $raw): HistoricalLocationSignificanceContent
    {
        return new HistoricalLocationSignificanceContent(
            architecturalSignificanceHeading: $raw['architectural_significance_heading'] ?? null,
            architecturalSignificanceText: $raw['architectural_significance_text'] ?? null,
            historicalSignificanceHeading: $raw['historical_significance_heading'] ?? null,
            historicalSignificanceText: $raw['historical_significance_text'] ?? null,
            significanceImage: $raw['significance_image'] ?? null,
        );
    }
}
