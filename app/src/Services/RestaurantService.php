<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\RestaurantRepository;
use App\Services\Interfaces\IRestaurantService;
use App\ViewModels\GradientSectionData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Restaurant\InstructionCardData;
use App\ViewModels\Restaurant\InstructionsSectionData;
use App\ViewModels\Restaurant\RestaurantCardData;
use App\ViewModels\Restaurant\RestaurantCardsSectionData;
use App\ViewModels\Restaurant\RestaurantPageViewModel;

/**
 * Service for preparing all data needed by the Restaurant page.
 *
 * This service uses TWO different data sources:
 *
 *  1. CMS (CmsItem table) — for page copy: titles, descriptions, images
 *     that an admin can edit. Used by: hero, gradient, intro, instructions.
 *
 *  2. Domain (Restaurant table) — for real restaurant business data:
 *     names, addresses, cuisine types, star ratings, card images.
 *     Used by: restaurant cards section.
 */
class RestaurantService implements IRestaurantService
{
    private const PAGE_SLUG = 'restaurant';

    // CMS section keys — must match the SectionKey values in the CmsSection table.
    private const SECTION_GRADIENT     = 'gradient_section';
    private const SECTION_INTRO_SPLIT  = 'intro_split_section';
    private const SECTION_INTRO_SPLIT2 = 'intro_split2_section';
    private const SECTION_INSTRUCTIONS = 'instructions_section';
    private const SECTION_CARDS        = 'restaurant_cards_section';

    // Fallback image shown when a CMS image path is missing or invalid.
    private const DEFAULT_IMAGE = '/assets/Image/Image (Yummy).png';
    private const VALID_IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif'];

    private CmsService $cmsService;
    private RestaurantRepository $restaurantRepository;

    public function __construct(
        ?CmsService $cmsService = null,
        ?RestaurantRepository $restaurantRepository = null
    ) {
        $this->cmsService = $cmsService ?? new CmsService();
        $this->restaurantRepository = $restaurantRepository ?? new RestaurantRepository();
    }

    // =====================================================================
    //  ENTRY POINT — builds the full page ViewModel
    // =====================================================================

    /**
     * Builds the complete page ViewModel consumed by the restaurant view.
     */
    public function getRestaurantPageData(): RestaurantPageViewModel
    {
        return new RestaurantPageViewModel(
            heroData:              $this->cmsService->buildHeroData(self::PAGE_SLUG, self::PAGE_SLUG),
            globalUi:              $this->cmsService->buildGlobalUiData(),
            gradientSection:       $this->buildGradientSection(),
            introSplitSection:     $this->buildIntroSplitSection(),
            introSplit2Section:    $this->buildIntroSplit2Section(),
            instructionsSection:   $this->buildInstructionsSection(),
            restaurantCardsSection: $this->buildRestaurantCardsSection(),
        );
    }

    // =====================================================================
    //  CMS-DRIVEN SECTIONS — text/images from CmsItem table
    //  Admin can edit these through the CMS dashboard.
    // =====================================================================

    private function buildGradientSection(): GradientSectionData
    {
        $cms = $this->getCmsSection(self::SECTION_GRADIENT);

        return new GradientSectionData(
            headingText:      $this->text($cms, 'gradient_heading'),
            subheadingText:   $this->text($cms, 'gradient_subheading'),
            backgroundImageUrl: $this->imagePath($cms, 'gradient_background_image'),
        );
    }

    private function buildIntroSplitSection(): IntroSplitSectionData
    {
        $cms     = $this->getCmsSection(self::SECTION_INTRO_SPLIT);
        $heading = $this->text($cms, 'intro_heading');
        $closing = $this->text($cms, 'intro_closing');

        return new IntroSplitSectionData(
            headingText:  $heading,
            bodyText:     $this->text($cms, 'intro_body'),
            imageUrl:     $this->imagePath($cms, 'intro_image'),
            imageAltText: $this->text($cms, 'intro_image_alt', $heading),
            subsections:  $this->buildIntroSubsections($cms),
            closingLine:  $closing !== '' ? $closing : null,
        );
    }

    /**
     * Reads subsection CMS keys (intro_sub1_heading, intro_sub1_text, etc.)
     * Returns null if no subsections exist in CMS.
     */
    private function buildIntroSubsections(array $cms): ?array
    {
        $subsections = [];

        for ($i = 1; $i <= 3; $i++) {
            $heading = $this->text($cms, 'intro_sub' . $i . '_heading');
            if ($heading !== '') {
                $subsections[] = [
                    'heading' => $heading,
                    'text'    => $this->text($cms, 'intro_sub' . $i . '_text'),
                ];
            }
        }

        return $subsections !== [] ? $subsections : null;
    }

    private function buildIntroSplit2Section(): ?IntroSplitSectionData
    {
        $cms = $this->getCmsSection(self::SECTION_INTRO_SPLIT2);

        if ($cms === []) {
            return null;
        }

        $heading = $this->text($cms, 'intro2_heading');

        return new IntroSplitSectionData(
            headingText:  $heading,
            bodyText:     $this->text($cms, 'intro2_body'),
            imageUrl:     $this->imagePath($cms, 'intro2_image'),
            imageAltText: $this->text($cms, 'intro2_image_alt', $heading),
        );
    }

    private function buildInstructionsSection(): ?InstructionsSectionData
    {
        $cms = $this->getCmsSection(self::SECTION_INSTRUCTIONS);

        if ($cms === []) {
            return null;
        }

        return new InstructionsSectionData(
            title: $this->text($cms, 'instructions_title'),
            cards: [
                new InstructionCardData('1', $this->text($cms, 'instructions_card_1_title'), $this->text($cms, 'instructions_card_1_text'), 'search'),
                new InstructionCardData('2', $this->text($cms, 'instructions_card_2_title'), $this->text($cms, 'instructions_card_2_text'), 'calendar'),
                new InstructionCardData('3', $this->text($cms, 'instructions_card_3_title'), $this->text($cms, 'instructions_card_3_text'), 'check'),
            ],
        );
    }

    // =====================================================================
    //  DOMAIN-DRIVEN SECTION — restaurant cards from the Restaurant table
    //  CMS only provides the section title + subtitle.
    //  The actual card data comes from the Restaurant DB table
    //  (joined with MediaAsset for images).
    // =====================================================================

    private function buildRestaurantCardsSection(): RestaurantCardsSectionData
    {
        $cms         = $this->getCmsSection(self::SECTION_CARDS);
        $restaurants = $this->restaurantRepository->findAllActive();

        return new RestaurantCardsSectionData(
            title:    $this->text($cms, 'cards_title', 'Explore the participant restaurants'),
            subtitle: $this->text($cms, 'cards_subtitle', 'Discover all restaurants participating in Yummy! Each one offers a special festival menu, unique flavors, and limited time slots throughout the weekend.'),
            filters:  $this->buildCuisineFilters($restaurants),
            cards:    $this->buildCards($restaurants),
        );
    }
    /**
     * Builds the filter button labels (e.g. "All", "Dutch", "French"...)
     * Why is this here and not in the repository?
     * Because it's presentation logic — it decides what labels to show
     * in the UI filter bar. The repository just returns raw data.
     *
     * @param  \App\Models\Restaurant[] $restaurants
     * @return string[]
     */
    private function buildCuisineFilters(array $restaurants): array
    {
        $unique = [];

        foreach ($restaurants as $restaurant) {
            foreach (explode(',', $restaurant->cuisineType) as $cuisine) {
                $cuisine = trim($cuisine);
                $key     = mb_strtolower($cuisine);

                // Skip empty values and duplicates (case-insensitive).
                if ($key !== '' && !isset($unique[$key])) {
                    $unique[$key] = $cuisine;
                }
            }
        }
        // Sort alphabetically, then prepend "All" as the first filter.
        $labels = array_values($unique);
        sort($labels, SORT_NATURAL | SORT_FLAG_CASE);

        return ['All', ...$labels];
    }

    /**
     * Converts Restaurant domain models into card ViewModels for the view.
     *
     * @param  \App\Models\Restaurant[] $restaurants
     * @return RestaurantCardData[]
     */
    private function buildCards(array $restaurants): array
    {
        $cards = [];

        foreach ($restaurants as $restaurant) {
            $cards[] = new RestaurantCardData(
                name:        $restaurant->name,
                cuisine:     $restaurant->cuisineType,
                address:     $this->buildAddress($restaurant),
                description: $this->cleanDescription($restaurant->descriptionHtml),
                rating:      $restaurant->stars ?? 0,
                image:       $restaurant->imagePath ?? self::DEFAULT_IMAGE,
            );
        }

        return $cards;
    }

    /**
     * Combines address line + city into one string: "Street 1, Haarlem".
     */
    private function buildAddress(\App\Models\Restaurant $restaurant): string
    {
        $address = trim($restaurant->addressLine);

        if ($restaurant->city !== '') {
            $address .= ', ' . $restaurant->city;
        }

        return $address;
    }

    /**
     * Strips HTML tags from the restaurant description so cards show plain text.
     * Returns empty string if the description is empty or just "<p></p>".
     */
    private function cleanDescription(string $html): string
    {
        $html = trim($html);

        if ($html === '' || $html === '<p></p>') {
            return '';
        }

        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/', ' ', $text) ?? $text);
    }

    // =====================================================================
    //  HELPERS
    // =====================================================================
    /**
     * Reads one CMS section for this page.
     * Returns an array like ['gradient_heading' => 'Some text', ...].
     */
    private function getCmsSection(string $sectionKey): array
    {
        return $this->cmsService->getSectionContent(self::PAGE_SLUG, $sectionKey);
    }

    /**
     * Gets a text value from a CMS section array.
     * Returns the default if the key is missing or empty.
     */
    private function text(array $cms, string $key, string $default = ''): string
    {
        $value = $cms[$key] ?? null;
        $result = is_string($value) && $value !== '' ? $value : $default;

        // The DB may store newlines as literal '\n' (backslash + n).
        // Convert them to real newlines so nl2br() works in the view.
        return str_replace('\n', "\n", $result);
    }

    /**
     * Gets an image path from a CMS section array.
     * Returns DEFAULT_IMAGE if the path is missing, empty, or invalid.
     */
    private function imagePath(array $cms, string $key): string
    {
        $path = (string)($cms[$key] ?? '');

        if ($path === '' || !str_starts_with($path, '/assets/')) {
            return self::DEFAULT_IMAGE;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($extension, self::VALID_IMAGE_EXTENSIONS, true)) {
            return self::DEFAULT_IMAGE;
        }

        return $path;
    }
}
