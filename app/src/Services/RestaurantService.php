<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\RestaurantImageRepository;
use App\Repositories\RestaurantRepository;
use App\Services\Interfaces\IRestaurantService;
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
    private const SECTION_DETAIL       = 'detail_section';

    // Fallback image shown when a CMS image path is missing or invalid.
    private const DEFAULT_IMAGE = '/assets/Image/Image (Yummy).png';
    private const VALID_IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif'];

    private CmsService $cmsService;
    private RestaurantRepository $restaurantRepository;
    private RestaurantImageRepository $restaurantImageRepository;

    public function __construct(
        ?CmsService $cmsService = null,
        ?RestaurantRepository $restaurantRepository = null,
        ?RestaurantImageRepository $restaurantImageRepository = null,
    ) {
        $this->cmsService = $cmsService ?? new CmsService();
        $this->restaurantRepository = $restaurantRepository ?? new RestaurantRepository();
        $this->restaurantImageRepository = $restaurantImageRepository ?? new RestaurantImageRepository();
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

    /**
     * Builds the detail page ViewModel for a single restaurant.
     * Returns null if the restaurant is not found.
     *
     * All detail content comes from the Restaurant domain table columns.
     * Images come from MediaAsset JOINs in the repository.
     */
    public function getRestaurantDetailData(int $id): ?RestaurantDetailViewModel
    {
        $restaurant = $this->restaurantRepository->findById($id);

        if ($restaurant === null) {
            return null;
        }

        $images = $this->restaurantImageRepository->findByRestaurantId($restaurant->restaurantId);
        $imagesByType = $this->groupImagesByType($images);

        // Read CMS labels for the detail page (admin-editable section titles).
        $cms = $this->getCmsSection(self::SECTION_DETAIL);

        // Build hero — subtitle template comes from CMS with {name} and {cuisine} placeholders.
        $subtitleTemplate = $this->text($cms, 'detail_hero_subtitle_template');
        $heroSubtitle = str_replace(
            ['{name}', '{cuisine}'],
            [$restaurant->name, $restaurant->cuisineType],
            $subtitleTemplate
        );

        $heroData = new HeroData(
            mainTitle: $restaurant->name,
            subtitle: $heroSubtitle,
            primaryButtonText: $this->text($cms, 'detail_hero_btn_primary'),
            primaryButtonLink: '#reservation',
            secondaryButtonText: $this->text($cms, 'detail_hero_btn_secondary'),
            secondaryButtonLink: '/restaurant',
            backgroundImageUrl: $restaurant->imagePath ?? self::DEFAULT_IMAGE,
            currentPage: self::PAGE_SLUG,
        );

        // Parse cuisine type string into individual tags for the menu section.
        $cuisineTags = array_map('trim', explode(',', $restaurant->cuisineType));
        $cuisineTags = array_filter($cuisineTags, fn(string $tag) => $tag !== '');

        // TODO: Replace with EventSession data when available.
        // For now, time slots and prices are per-restaurant hardcoded lookups.
        $restaurantData = $this->getRestaurantScheduleData($restaurant->name);
        $timeSlots = $restaurantData['timeSlots'];
        $priceCards = $restaurantData['priceCards'];

        return new RestaurantDetailViewModel(
            heroData: $heroData,
            globalUi: $this->cmsService->buildGlobalUiData(),

            id:          $restaurant->restaurantId,
            name:        $restaurant->name,
            cuisine:     $restaurant->cuisineType,
            address:     $this->buildAddress($restaurant),
            description: $this->cleanDescription($restaurant->descriptionHtml),
            rating:      $restaurant->stars ?? 0,
            image:       $restaurant->imagePath ?? self::DEFAULT_IMAGE,

            phone:   $restaurant->phone ?? '',
            email:   $restaurant->email ?? '',
            website: $restaurant->website ?? '',

            aboutText:  str_replace('\n', "\n", $restaurant->aboutText ?? ''),
            aboutImage: ($imagesByType['about'] ?? [])[0] ?? self::DEFAULT_IMAGE,

            chefName:  $restaurant->chefName ?? '',
            chefText:  str_replace('\n', "\n", $restaurant->chefText ?? ''),
            chefImage: ($imagesByType['chef'] ?? [])[0] ?? self::DEFAULT_IMAGE,

            menuDescription: $restaurant->menuDescription ?? '',
            cuisineTags:     array_values($cuisineTags),
            menuImages:      $imagesByType['menu'] ?? [self::DEFAULT_IMAGE, self::DEFAULT_IMAGE],

            locationDescription: str_replace('\n', "\n", $restaurant->locationDescription ?? ''),
            mapEmbedUrl:         $restaurant->mapEmbedUrl ?? '',

            michelinStars:       $restaurant->michelinStars ?? 0,
            seatsPerSession:     $restaurant->seatsPerSession ?? 0,
            durationMinutes:     $restaurant->durationMinutes ?? 0,
            specialRequestsNote: $restaurant->specialRequestsNote ?? '',

            galleryImages: $imagesByType['gallery'] ?? [self::DEFAULT_IMAGE],

            reservationImage: ($imagesByType['reservation'] ?? [])[0] ?? self::DEFAULT_IMAGE,
            timeSlots:        $timeSlots,
            priceCards:       $priceCards,

            // CMS labels — admin-editable section titles and labels
            labelContactTitle:    $this->text($cms, 'detail_contact_title'),
            labelAddress:         $this->text($cms, 'detail_label_address'),
            labelContact:         $this->text($cms, 'detail_label_contact'),
            labelOpenHours:       $this->text($cms, 'detail_label_open_hours'),
            labelPracticalTitle:  $this->text($cms, 'detail_practical_title'),
            labelPriceFood:       $this->text($cms, 'detail_label_price_food'),
            labelRating:          $this->text($cms, 'detail_label_rating'),
            labelSpecialRequests: $this->text($cms, 'detail_label_special_requests'),
            labelGalleryTitle:    $this->text($cms, 'detail_gallery_title'),
            labelAboutPrefix:     $this->text($cms, 'detail_about_title_prefix'),
            labelChefTitle:       $this->text($cms, 'detail_chef_title'),
            labelMenuTitle:       $this->text($cms, 'detail_menu_title'),
            labelCuisineType:     $this->text($cms, 'detail_menu_cuisine_label'),
            labelLocationTitle:   $this->text($cms, 'detail_location_title'),
            labelLocationAddress: $this->text($cms, 'detail_location_address_label'),
            labelReservationTitle: $this->text($cms, 'detail_reservation_title'),
            labelReservationDesc: $this->text($cms, 'detail_reservation_description'),
            labelSlotsLabel:      $this->text($cms, 'detail_reservation_slots_label'),
            labelReservationNote: $this->text($cms, 'detail_reservation_note'),
            labelReservationBtn:  $this->text($cms, 'detail_reservation_btn'),
            labelDuration:        $this->text($cms, 'detail_label_duration'),
            labelSeats:           $this->text($cms, 'detail_label_seats'),
            labelFestivalRated:   $this->text($cms, 'detail_label_festival_rated'),
            labelMichelin:        $this->text($cms, 'detail_label_michelin'),
            labelMapFallback:     $this->text($cms, 'detail_map_fallback_text'),
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
            title:    $this->text($cms, 'cards_title'),
            subtitle: $this->text($cms, 'cards_subtitle'),
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
                id:          $restaurant->restaurantId,
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
     * Groups restaurant images by type and returns file paths.
     *
     * @param \App\Models\RestaurantImage[] $images
     * @return array<string, string[]> image type => file paths
     */
    private function groupImagesByType(array $images): array
    {
        $grouped = [];
        foreach ($images as $image) {
            $grouped[$image->imageType][] = $image->filePath ?? self::DEFAULT_IMAGE;
        }
        return $grouped;
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

    /**
     * Returns per-restaurant time slots and price cards.
     * TODO: Replace with EventSession/pricing data from database.
     */
    private function getRestaurantScheduleData(string $name): array
    {
        $schedules = [
            'Ratatouille' => [
                'timeSlots' => ['17:00', '19:15', '21:30'],
                'priceCards' => [
                    ['label' => 'Per adult (drinks not included)', 'price' => '€ 45.00'],
                    ['label' => 'Under 12 (drinks not included)',  'price' => '€ 22.50'],
                ],
            ],
            'Urban Frenchy Bistro Toujours' => [
                'timeSlots' => ['17:30', '19:15', '21:00'],
                'priceCards' => [
                    ['label' => 'Per adult (drinks not included)', 'price' => '€ 35.00'],
                    ['label' => 'Under 12 (drinks not included)',  'price' => '€ 17.50'],
                ],
            ],
        ];

        return $schedules[$name] ?? [
            'timeSlots' => [],
            'priceCards' => [],
        ];
    }
}
