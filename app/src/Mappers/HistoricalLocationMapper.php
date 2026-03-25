<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Models\HistoricalLocationFactsContent;
use App\Models\HistoricalLocationHeroContent;
use App\Models\HistoricalLocationIntroContent;
use App\DTOs\Pages\HistoricalLocationPageData;
use App\Models\HistoricalLocationSignificanceContent;
use App\Constants\HistoryPageConstants;
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
        $heroData = CmsMapper::toHeroData($data->heroSection, HistoryPageConstants::CURRENT_PAGE);
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
            cms: CmsMapper::toCmsData($heroData, $globalUi),
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
            currentPage: HistoryPageConstants::CURRENT_PAGE,
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
}
