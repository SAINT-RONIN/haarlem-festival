<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Constants\DancePageConstants;
use App\DTOs\Domain\Events\ArtistCardRecord;
use App\DTOs\Domain\Pages\DancePageData;
use App\Helpers\TextHelper;
use App\ViewModels\Dance\DanceArtistCardData;
use App\ViewModels\Dance\DancePageViewModel;
use App\ViewModels\Dance\HeadlinersData;
use App\ViewModels\Dance\SupportingArtistsData;
use App\ViewModels\Schedule\ScheduleSectionViewModel;

/**
 * Transforms DancePageData domain model into ViewModels for the public Dance landing page,
 * splitting artists into headliners (first N by CardSortOrder) and supporting (the rest).
 */
final class DanceMapper
{
    public static function toPageViewModel(
        DancePageData $domain,
        ?ScheduleSectionViewModel $scheduleSection = null,
        bool $isLoggedIn = false,
    ): DancePageViewModel {
        $heroData  = CmsMapper::toHeroData($domain->heroSection, DancePageConstants::PAGE_SLUG);
        $globalUi  = CmsMapper::toGlobalUiData($domain->globalUiContent, $isLoggedIn);

        return new DancePageViewModel(
            heroData: $heroData,
            globalUi: $globalUi,
            gradientSection: CmsMapper::toGradientSection(
                $domain->gradientSection,
                DancePageConstants::DEFAULT_GRADIENT_BACKGROUND_IMAGE,
            ),
            introSplitSection: CmsMapper::toIntroSplitSection(
                $domain->introSection,
                DancePageConstants::DEFAULT_INTRO_IMAGE,
                DancePageConstants::DEFAULT_INTRO_IMAGE_ALT,
            ),
            headlinersData: self::buildHeadlinersData($domain),
            supportingArtistsData: self::buildSupportingArtistsData($domain),
            scheduleSection: $scheduleSection,
        );
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private static function buildHeadlinersData(DancePageData $domain): HeadlinersData
    {
        $headliners = array_slice($domain->danceArtists, 0, DancePageConstants::HEADLINER_MAX_COUNT);

        return new HeadlinersData(
            headingText: $domain->headlinersSection->headlinersHeading ?? 'HEADLINERS',
            headliners: self::buildHeadlinerCards($headliners),
        );
    }

    private static function buildSupportingArtistsData(DancePageData $domain): SupportingArtistsData
    {
        $supporting = array_slice($domain->danceArtists, DancePageConstants::HEADLINER_MAX_COUNT);

        return new SupportingArtistsData(
            headingText: $domain->artistsSection->artistsHeading ?? 'Supporting Artists',
            artists: self::buildSupportingCards($supporting),
        );
    }

    /**
     * @param ArtistCardRecord[] $artists
     * @return DanceArtistCardData[]
     */
    private static function buildHeadlinerCards(array $artists): array
    {
        return array_map(static fn(ArtistCardRecord $a): DanceArtistCardData => self::toArtistCardData($a), $artists);
    }

    /**
     * @param ArtistCardRecord[] $artists
     * @return DanceArtistCardData[]
     */
    private static function buildSupportingCards(array $artists): array
    {
        return array_map(static fn(ArtistCardRecord $a): DanceArtistCardData => self::toArtistCardData($a), $artists);
    }

    private static function toArtistCardData(ArtistCardRecord $a): DanceArtistCardData
    {
        return new DanceArtistCardData(
            name: $a->artistName,
            genre: $a->artistStyle,
            imageUrl: TextHelper::firstNonEmpty($a->imageUrl, DancePageConstants::DEFAULT_HERO_BACKGROUND_IMAGE),
            profileUrl: '/dance/' . $a->eventSlug,
        );
    }
}
