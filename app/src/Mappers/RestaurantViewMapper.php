<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Constants\RestaurantPageConstants;
use App\DTOs\Cms\GradientSectionContent;
use App\DTOs\Domain\Pages\RestaurantPageData;
use App\DTOs\Cms\GlobalUiContent;
use App\Models\Restaurant;
use App\ViewModels\GradientSectionData;
use App\ViewModels\HeroData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Restaurant\InstructionCardData;
use App\ViewModels\Restaurant\InstructionsSectionData;
use App\ViewModels\Restaurant\RestaurantCardData;
use App\ViewModels\Restaurant\RestaurantCardsSectionData;
use App\ViewModels\Restaurant\RestaurantDetailViewModel;
use App\ViewModels\Restaurant\RestaurantPageViewModel;

final class RestaurantViewMapper
{
    private const VALID_IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif'];

    // ── Listing page ──────────────────────────────────────────────────

    public static function toPageViewModel(RestaurantPageData $data, bool $isLoggedIn): RestaurantPageViewModel
    {
        $heroData = CmsMapper::toHeroData($data->heroContent, 'restaurant');
        $globalUi = CmsMapper::toGlobalUiData($data->globalUiContent, $isLoggedIn);
        $cuisines = self::extractCuisineFilters($data->restaurants);

        return new RestaurantPageViewModel(
            heroData: $heroData,
            globalUi: $globalUi,
            gradientSection: self::toGradientSection($data->gradientSection),
            introSplitSection: self::toIntroSplitSection($data->introSplitContent),
            introSplit2Section: self::toIntroSplit2Section($data->introSplit2Content),
            instructionsSection: self::toInstructionsSection($data->instructionsContent),
            restaurantCardsSection: self::toRestaurantCardsSection($data->cardsContent, $data->restaurants, $cuisines),
        );
    }

    // ── Detail page ───────────────────────────────────────────────────

    /**
     * @param array<string, ?string> $labels
     * @param string[] $validDates
     */
    public static function toDetailViewModel(
        Restaurant $restaurant,
        array $labels,
        GlobalUiContent $globalUiContent,
        bool $isLoggedIn,
        array $validDates,
    ): RestaurantDetailViewModel {
        $globalUi = CmsMapper::toGlobalUiData($globalUiContent, $isLoggedIn);
        $timeSlots = self::parseTimeSlots($restaurant->timeSlots);
        $priceCards = self::buildPriceCards($restaurant, $labels);

        $reservationImage = self::validateImagePath($restaurant->reservationImage ?? '');
        if ($reservationImage === RestaurantPageConstants::DEFAULT_IMAGE && $restaurant->featuredImagePath !== null) {
            $reservationImage = self::validateImagePath($restaurant->featuredImagePath);
        }

        return new RestaurantDetailViewModel(
            heroData: self::toDetailHeroData($restaurant, $labels),
            globalUi: $globalUi,
            restaurant: $restaurant,
            labels: $labels,
            timeSlots: $timeSlots,
            priceCards: $priceCards,
            validDates: $validDates,
            menuImages: self::collectImages([$restaurant->menuImage1, $restaurant->menuImage2]),
            galleryImages: self::collectImages([$restaurant->galleryImage1, $restaurant->galleryImage2, $restaurant->galleryImage3]),
            address: self::formatAddress($restaurant->addressLine, $restaurant->city),
            reservationImage: $reservationImage,
        );
    }

    // ── Detail hero ───────────────────────────────────────────────────

    private static function toDetailHeroData(Restaurant $r, array $labels): HeroData
    {
        $subtitleTemplate = $labels['detail_hero_subtitle_template'] ?? '';
        $heroSubtitle = str_replace(
            ['{name}', '{cuisine}'],
            [$r->name, $r->cuisineType ?? ''],
            $subtitleTemplate,
        );

        return new HeroData(
            mainTitle: $r->name,
            subtitle: $heroSubtitle,
            primaryButtonText: $labels['detail_hero_btn_primary'] ?? '',
            primaryButtonLink: '#reservation',
            secondaryButtonText: $labels['detail_hero_btn_secondary'] ?? '',
            secondaryButtonLink: '/restaurant',
            backgroundImageUrl: $r->featuredImagePath ?? RestaurantPageConstants::DEFAULT_IMAGE,
            currentPage: 'restaurant',
        );
    }

    // ── Helpers ────────────────────────────────────────────────────────

    /** @return string[] */
    private static function parseTimeSlots(?string $raw): array
    {
        if ($raw === null || $raw === '') {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }

    /**
     * @param array<string, ?string> $labels
     * @return array{label: string, price: string}[]
     */
    private static function buildPriceCards(Restaurant $r, array $labels): array
    {
        if ($r->priceAdult <= 0) {
            return [];
        }

        return [
            ['label' => $labels['detail_label_price_adult'] ?? 'Per adult', 'price' => 'EUR ' . number_format($r->priceAdult, 2)],
            ['label' => $labels['detail_label_price_child'] ?? 'Under 12', 'price' => 'EUR ' . number_format($r->priceAdult / 2, 2)],
        ];
    }

    private static function formatAddress(?string $addressLine, ?string $city): string
    {
        $parts = array_filter(
            [trim((string) $addressLine), trim((string) $city)],
            static fn(string $part): bool => $part !== '',
        );

        return implode(', ', $parts);
    }

    /**
     * @param (?string)[] $paths
     * @return string[]
     */
    private static function collectImages(array $paths): array
    {
        $validated = [];
        foreach ($paths as $path) {
            if ($path !== null && $path !== '') {
                $img = self::validateImagePath($path);
                if ($img !== RestaurantPageConstants::DEFAULT_IMAGE) {
                    $validated[] = $img;
                }
            }
        }

        return $validated;
    }

    private static function validateImagePath(string $path): string
    {
        if ($path === '' || !str_starts_with($path, '/assets/')) {
            return RestaurantPageConstants::DEFAULT_IMAGE;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($extension, self::VALID_IMAGE_EXTENSIONS, true)) {
            return RestaurantPageConstants::DEFAULT_IMAGE;
        }

        return $path;
    }

    /** Strips HTML tags and normalises whitespace. */
    private static function cleanDescription(string $html): string
    {
        $html = trim($html);
        if ($html === '' || $html === '<p></p>') {
            return '';
        }

        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/', ' ', $text) ?? $text);
    }

    // ── Cuisine filters ───────────────────────────────────────────────

    /**
     * @param Restaurant[] $restaurants
     * @return string[]
     */
    private static function extractCuisineFilters(array $restaurants): array
    {
        $unique = [];
        foreach ($restaurants as $restaurant) {
            foreach ($restaurant->cuisineTags as $tag) {
                $key = mb_strtolower($tag);
                if (!isset($unique[$key])) {
                    $unique[$key] = mb_convert_case($key, MB_CASE_TITLE, 'UTF-8');
                }
            }
        }

        $labels = array_values($unique);
        sort($labels, SORT_NATURAL | SORT_FLAG_CASE);

        return ['All', ...$labels];
    }

    // ── Listing page section builders ─────────────────────────────────

    private static function toGradientSection(GradientSectionContent $cms): GradientSectionData
    {
        return new GradientSectionData(
            headingText: $cms->gradientHeading ?? '',
            subheadingText: $cms->gradientSubheading ?? '',
            backgroundImageUrl: self::validateImagePath($cms->gradientBackgroundImage ?? ''),
        );
    }

    /** @param array<string, ?string> $cms */
    private static function toIntroSplitSection(array $cms): IntroSplitSectionData
    {
        $heading = $cms['intro_heading'] ?? '';

        return new IntroSplitSectionData(
            headingText: $heading,
            bodyText: $cms['intro_body'] ?? '',
            imageUrl: self::validateImagePath($cms['intro_image'] ?? ''),
            imageAltText: $cms['intro_image_alt'] ?? $heading,
            subsections: self::parseSubsections($cms),
            closingLine: $cms['intro_closing'] ?? null,
        );
    }

    /**
     * @return array{heading: string, text: string}[]|null
     */
    private static function parseSubsections(array $cms): ?array
    {
        $subsections = [];
        for ($i = 1; $i <= 10; $i++) {
            $heading = $cms["intro_sub{$i}_heading"] ?? null;
            $text = $cms["intro_sub{$i}_text"] ?? null;
            if ($heading === null && $text === null) {
                break;
            }
            $subsections[] = ['heading' => $heading ?? '', 'text' => $text ?? ''];
        }

        return $subsections !== [] ? $subsections : null;
    }

    /** @param array<string, ?string> $cms */
    private static function toIntroSplit2Section(array $cms): ?IntroSplitSectionData
    {
        if (!isset($cms['intro2_heading']) && !isset($cms['intro2_body'])) {
            return null;
        }

        $heading = $cms['intro2_heading'] ?? '';

        return new IntroSplitSectionData(
            headingText: $heading,
            bodyText: $cms['intro2_body'] ?? '',
            imageUrl: self::validateImagePath($cms['intro2_image'] ?? ''),
            imageAltText: $cms['intro2_image_alt'] ?? $heading,
        );
    }

    /** @param array<string, ?string> $cms */
    private static function toInstructionsSection(array $cms): ?InstructionsSectionData
    {
        if (!isset($cms['instructions_title'])) {
            return null;
        }

        return new InstructionsSectionData(
            title: $cms['instructions_title'],
            cards: [
                new InstructionCardData('1', $cms['instructions_card_1_title'] ?? '', $cms['instructions_card_1_text'] ?? '', 'search'),
                new InstructionCardData('2', $cms['instructions_card_2_title'] ?? '', $cms['instructions_card_2_text'] ?? '', 'calendar'),
                new InstructionCardData('3', $cms['instructions_card_3_title'] ?? '', $cms['instructions_card_3_text'] ?? '', 'check'),
            ],
        );
    }

    /**
     * @param array<string, ?string> $cms
     * @param Restaurant[] $restaurants
     * @param string[] $cuisines
     */
    private static function toRestaurantCardsSection(array $cms, array $restaurants, array $cuisines): RestaurantCardsSectionData
    {
        return new RestaurantCardsSectionData(
            title: $cms['cards_title'] ?? '',
            subtitle: $cms['cards_subtitle'] ?? '',
            filters: $cuisines,
            cards: self::buildCards($restaurants),
        );
    }

    /**
     * @param Restaurant[] $restaurants
     * @return RestaurantCardData[]
     */
    private static function buildCards(array $restaurants): array
    {
        $cards = [];
        foreach ($restaurants as $r) {
            $cuisine = trim($r->cuisineType ?? '');
            $cards[] = new RestaurantCardData(
                id: $r->id,
                name: $r->name,
                cuisine: $cuisine,
                cuisineTags: array_map('mb_strtolower', $r->cuisineTags),
                address: self::formatAddress($r->addressLine, $r->city),
                description: self::buildCardDescription($r),
                rating: $r->stars,
                image: $r->featuredImagePath ?? RestaurantPageConstants::DEFAULT_IMAGE,
                slug: $r->slug,
                isVegan: str_contains(mb_strtolower($cuisine), 'vegan'),
            );
        }

        return $cards;
    }

    private static function buildCardDescription(Restaurant $r): string
    {
        $candidates = [$r->aboutText, $r->shortDescription, $r->locationDescription];

        foreach ($candidates as $candidate) {
            $description = self::cleanDescription((string) ($candidate ?? ''));
            if ($description !== '') {
                return $description;
            }
        }

        return '';
    }
}
