<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Models\CuisineType;
use App\Models\Restaurant;
use App\Models\RestaurantCardsSectionContent;
use App\DTOs\Pages\RestaurantDetailData;
use App\Models\RestaurantDetailSectionContent;
use App\Models\GradientSectionContent;
use App\Models\RestaurantInstructionsSectionContent;
use App\Models\RestaurantIntroSectionContent;
use App\Models\RestaurantIntroSplit2SectionContent;
use App\DTOs\Pages\RestaurantPageData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Restaurant\InstructionCardData;
use App\ViewModels\Restaurant\InstructionsSectionData;
use App\ViewModels\Restaurant\RestaurantCardData;
use App\ViewModels\Restaurant\RestaurantCardsSectionData;
use App\ViewModels\Restaurant\RestaurantDetailViewModel;
use App\ViewModels\Restaurant\RestaurantPageViewModel;

/**
 * Transforms restaurant domain data into page-level ViewModels.
 * Delegates content extraction to RestaurantContentParser.
 */
final class RestaurantViewMapper
{
    /** Builds the restaurant listing page ViewModel. */
    public static function toPageViewModel(RestaurantPageData $data, bool $isLoggedIn): RestaurantPageViewModel
    {
        $heroData = CmsMapper::toHeroData($data->heroContent, 'restaurant');
        $globalUi = CmsMapper::toGlobalUiData($data->globalUiContent, $isLoggedIn);

        return new RestaurantPageViewModel(
            heroData:               $heroData,
            globalUi:               $globalUi,
            cms:                    CmsMapper::toCmsData($heroData, $globalUi),
            gradientSection:        self::toGradientSection($data->gradientSection),
            introSplitSection:      self::toIntroSplitSection($data->introSplitSection),
            introSplit2Section:     self::toIntroSplit2Section($data->introSplit2Section),
            instructionsSection:    self::toInstructionsSection($data->instructionsSection),
            restaurantCardsSection: self::toRestaurantCardsSection($data->cardsSection, $data->restaurants, $data->cuisinesByRestaurant),
        );
    }

    /** Builds the restaurant detail page ViewModel. */
    public static function toDetailViewModel(RestaurantDetailData $data, bool $isLoggedIn): RestaurantDetailViewModel
    {
        $restaurant  = $data->restaurant;
        $cms         = $data->cms;
        $cuisineString = RestaurantContentParser::buildCuisineString($data->cuisineTypes);
        $heroData    = self::toDetailHeroData($restaurant, $cms, $cuisineString);
        $globalUi    = CmsMapper::toGlobalUiData($data->globalUiContent, $isLoggedIn);

        return new RestaurantDetailViewModel(...array_merge(
            ['heroData' => $heroData, 'globalUi' => $globalUi, 'cms' => CmsMapper::toCmsData($heroData, $globalUi)],
            RestaurantContentParser::buildDetailDomainFields($restaurant, $data, $cuisineString),
            RestaurantContentParser::buildDetailCmsLabels($cms),
        ));
    }

    /** Builds the hero section for a restaurant detail page. */
    private static function toDetailHeroData(Restaurant $restaurant, RestaurantDetailSectionContent $cms, string $cuisineString): HeroData
    {
        $subtitleTemplate = $cms->detailHeroSubtitleTemplate ?? '';
        $heroSubtitle = str_replace(
            ['{name}', '{cuisine}'],
            [$restaurant->name, $cuisineString],
            $subtitleTemplate
        );

        return new HeroData(
            mainTitle:           $restaurant->name,
            subtitle:            $heroSubtitle,
            primaryButtonText:   $cms->detailHeroBtnPrimary ?? '',
            primaryButtonLink:   '#reservation',
            secondaryButtonText: $cms->detailHeroBtnSecondary ?? '',
            secondaryButtonLink: '/restaurant',
            backgroundImageUrl:  $restaurant->imagePath ?? RestaurantContentParser::DEFAULT_IMAGE,
            currentPage:         'restaurant',
        );
    }

    /** Maps gradient CMS content to a gradient section ViewModel. */
    private static function toGradientSection(GradientSectionContent $cms): GradientSectionData
    {
        return new GradientSectionData(
            headingText:        $cms->gradientHeading ?? '',
            subheadingText:     $cms->gradientSubheading ?? '',
            backgroundImageUrl: RestaurantContentParser::validateImagePath($cms->gradientBackgroundImage ?? ''),
        );
    }

    /** Maps intro CMS content to an intro-split section ViewModel with parsed body. */
    private static function toIntroSplitSection(RestaurantIntroSectionContent $cms): IntroSplitSectionData
    {
        $heading = $cms->introHeading ?? '';
        $parsed  = RestaurantContentParser::parseIntroBody($cms->introBody ?? '');
        $closing = $cms->introClosing ?? '';

        return new IntroSplitSectionData(
            headingText:  $heading,
            bodyText:     $parsed['bodyText'],
            imageUrl:     RestaurantContentParser::validateImagePath($cms->introImage ?? ''),
            imageAltText: $cms->introImageAlt ?? $heading,
            subsections:  $parsed['subsections'],
            closingLine:  $closing !== '' ? $closing : $parsed['closingLine'],
        );
    }

    /** Maps second intro CMS content to an intro-split section ViewModel, or null if empty. */
    private static function toIntroSplit2Section(RestaurantIntroSplit2SectionContent $cms): ?IntroSplitSectionData
    {
        if ($cms->intro2Heading === null && $cms->intro2Body === null) {
            return null;
        }

        $heading = $cms->intro2Heading ?? '';

        return new IntroSplitSectionData(
            headingText:  $heading,
            bodyText:     $cms->intro2Body ?? '',
            imageUrl:     RestaurantContentParser::validateImagePath($cms->intro2Image ?? ''),
            imageAltText: $cms->intro2ImageAlt ?? $heading,
        );
    }

    /** Maps instructions CMS content to an instructions section ViewModel with 3 cards. */
    private static function toInstructionsSection(RestaurantInstructionsSectionContent $cms): ?InstructionsSectionData
    {
        if ($cms->instructionsTitle === null) {
            return null;
        }

        return new InstructionsSectionData(
            title: $cms->instructionsTitle,
            cards: [
                new InstructionCardData('1', $cms->instructionsCard1Title ?? '', $cms->instructionsCard1Text ?? '', 'search'),
                new InstructionCardData('2', $cms->instructionsCard2Title ?? '', $cms->instructionsCard2Text ?? '', 'calendar'),
                new InstructionCardData('3', $cms->instructionsCard3Title ?? '', $cms->instructionsCard3Text ?? '', 'check'),
            ],
        );
    }

    /**
     * Builds the restaurant cards section with cuisine filters and card data.
     *
     * @param Restaurant[] $restaurants
     * @param array<int, CuisineType[]> $cuisinesByRestaurant
     */
    private static function toRestaurantCardsSection(RestaurantCardsSectionContent $cms, array $restaurants, array $cuisinesByRestaurant): RestaurantCardsSectionData
    {
        return new RestaurantCardsSectionData(
            title:    $cms->cardsTitle ?? '',
            subtitle: $cms->cardsSubtitle ?? '',
            filters:  self::buildCuisineFilters($restaurants, $cuisinesByRestaurant),
            cards:    self::buildCards($restaurants, $cuisinesByRestaurant),
        );
    }

    /**
     * Extracts and sorts unique cuisine type labels for the filter bar.
     *
     * @param Restaurant[] $restaurants
     * @param array<int, CuisineType[]> $cuisinesByRestaurant
     */
    private static function buildCuisineFilters(array $restaurants, array $cuisinesByRestaurant): array
    {
        $unique = [];

        foreach ($restaurants as $restaurant) {
            $cuisineTypes = $cuisinesByRestaurant[$restaurant->restaurantId] ?? [];
            foreach ($cuisineTypes as $cuisineType) {
                $key = mb_strtolower($cuisineType->name);
                if ($key !== '' && !isset($unique[$key])) {
                    $unique[$key] = $cuisineType->name;
                }
            }
        }

        $labels = array_values($unique);
        sort($labels, SORT_NATURAL | SORT_FLAG_CASE);

        return ['All', ...$labels];
    }

    /**
     * Builds card ViewModels for each restaurant.
     *
     * @param Restaurant[] $restaurants
     * @param array<int, CuisineType[]> $cuisinesByRestaurant
     * @return RestaurantCardData[]
     */
    private static function buildCards(array $restaurants, array $cuisinesByRestaurant): array
    {
        $cards = [];

        foreach ($restaurants as $restaurant) {
            $cuisineTypes = $cuisinesByRestaurant[$restaurant->restaurantId] ?? [];
            $cuisineString = implode(', ', array_map(fn(CuisineType $c) => $c->name, $cuisineTypes));

            $cards[] = new RestaurantCardData(
                id:          $restaurant->restaurantId,
                name:        $restaurant->name,
                cuisine:     $cuisineString,
                address:     RestaurantContentParser::buildAddress($restaurant),
                description: RestaurantContentParser::cleanDescription($restaurant->descriptionHtml),
                rating:      $restaurant->stars ?? 0,
                image:       $restaurant->imagePath ?? RestaurantContentParser::DEFAULT_IMAGE,
            );
        }

        return $cards;
    }
}
