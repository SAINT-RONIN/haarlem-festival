<?php

declare(strict_types=1);

namespace App\Mappers;

use App\DTOs\Cms\HistoricalLocationFactsContent;
use App\DTOs\Cms\HistoricalLocationHeroContent;
use App\DTOs\Cms\HistoricalLocationIntroContent;
use App\DTOs\Cms\HistoricalLocationSignificanceContent;

/**
 * Maps raw CMS arrays into HistoricalLocation content models.
 */
final class HistoricalLocationContentMapper
{
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
