<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\RestaurantRepository;
use App\Services\Interfaces\IRestaurantService;

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

    public function __construct(
        ?CmsService $cmsService = null,
        ?RestaurantRepository $restaurantRepository = null
    ) {
        $this->cmsService = $cmsService ?? new CmsService();
        $this->restaurantRepository = $restaurantRepository ?? new RestaurantRepository();
    }

    // =====================================================================
    //  ENTRY POINTS — return plain business data for the mapper
    // =====================================================================

    /**
     * Returns all business data needed by the restaurant listing page.
     * The mapper converts this data into ViewModels for the view.
     */
    public function getRestaurantPageData(): array
    {
        $restaurants = $this->restaurantRepository->findAllActive();

        return [
            'gradientCms'      => $this->getGradientData(),
            'introCms'         => $this->getIntroSplitData(),
            'intro2Cms'        => $this->getIntroSplit2Data(),
            'instructionsCms'  => $this->getInstructionsData(),
            'cardsCms'         => $this->getCardsSectionData(),
            'restaurants'      => $restaurants,
            'cuisineFilters'   => $this->buildCuisineFilters($restaurants),
            'cards'            => $this->buildCards($restaurants),
        ];
    }

    /**
     * Returns all business data needed by a single restaurant detail page.
     * Returns null if the restaurant is not found.
     *
     * The mapper converts this data into ViewModels for the view.
     */
    public function getRestaurantDetailData(int $id): ?array
    {
        $restaurant = $this->restaurantRepository->findById($id);

        if ($restaurant === null) {
            return null;
        }

        // Read CMS labels for the detail page (admin-editable section titles).
        $cms = $this->getCmsSection(self::SECTION_DETAIL);

        // Build hero subtitle from CMS template with placeholders.
        $subtitleTemplate = $this->text($cms, 'detail_hero_subtitle_template');
        $heroSubtitle = str_replace(
            ['{name}', '{cuisine}'],
            [$restaurant->name, $restaurant->cuisineType],
            $subtitleTemplate
        );

        // Parse cuisine type string into individual tags for the menu section.
        $cuisineTags = array_map('trim', explode(',', $restaurant->cuisineType));
        $cuisineTags = array_values(array_filter($cuisineTags, fn(string $tag) => $tag !== ''));

        // TODO: Replace with EventSession data when available.
        $scheduleData = $this->getRestaurantScheduleData($restaurant->name);

        return [
            // Domain data (from Restaurant table)
            'restaurant'      => $restaurant,
            'address'         => $this->buildAddress($restaurant),
            'cleanDescription' => $this->cleanDescription($restaurant->descriptionHtml),
            'cuisineTags'     => $cuisineTags,
            'heroSubtitle'    => $heroSubtitle,
            'timeSlots'       => $scheduleData['timeSlots'],
            'priceCards'      => $scheduleData['priceCards'],

            // CMS labels (admin-editable section titles and labels)
            'cmsLabels' => [
                'heroBtnPrimary'    => $this->text($cms, 'detail_hero_btn_primary'),
                'heroBtnSecondary'  => $this->text($cms, 'detail_hero_btn_secondary'),
                'contactTitle'      => $this->text($cms, 'detail_contact_title'),
                'labelAddress'      => $this->text($cms, 'detail_label_address'),
                'labelContact'      => $this->text($cms, 'detail_label_contact'),
                'labelOpenHours'    => $this->text($cms, 'detail_label_open_hours'),
                'practicalTitle'    => $this->text($cms, 'detail_practical_title'),
                'labelPriceFood'    => $this->text($cms, 'detail_label_price_food'),
                'labelRating'       => $this->text($cms, 'detail_label_rating'),
                'labelSpecialReqs'  => $this->text($cms, 'detail_label_special_requests'),
                'galleryTitle'      => $this->text($cms, 'detail_gallery_title'),
                'aboutTitlePrefix'  => $this->text($cms, 'detail_about_title_prefix'),
                'chefTitle'         => $this->text($cms, 'detail_chef_title'),
                'menuTitle'         => $this->text($cms, 'detail_menu_title'),
                'cuisineLabel'      => $this->text($cms, 'detail_menu_cuisine_label'),
                'locationTitle'     => $this->text($cms, 'detail_location_title'),
                'locationAddrLabel' => $this->text($cms, 'detail_location_address_label'),
                'reservationTitle'  => $this->text($cms, 'detail_reservation_title'),
                'reservationDesc'   => $this->text($cms, 'detail_reservation_description'),
                'slotsLabel'        => $this->text($cms, 'detail_reservation_slots_label'),
                'reservationNote'   => $this->text($cms, 'detail_reservation_note'),
                'reservationBtn'    => $this->text($cms, 'detail_reservation_btn'),
                'labelDuration'     => $this->text($cms, 'detail_label_duration'),
                'labelSeats'        => $this->text($cms, 'detail_label_seats'),
                'festivalRated'     => $this->text($cms, 'detail_label_festival_rated'),
                'labelMichelin'     => $this->text($cms, 'detail_label_michelin'),
                'mapFallback'       => $this->text($cms, 'detail_map_fallback_text'),
            ],
        ];
    }

    // =====================================================================
    //  CMS-DRIVEN SECTIONS — text/images from CmsItem table
    //  Admin can edit these through the CMS dashboard.
    // =====================================================================

    /**
     * Returns gradient section CMS content as a plain array.
     */
    private function getGradientData(): array
    {
        $cms = $this->getCmsSection(self::SECTION_GRADIENT);

        return [
            'heading'         => $this->text($cms, 'gradient_heading'),
            'subheading'      => $this->text($cms, 'gradient_subheading'),
            'backgroundImage' => $this->imagePath($cms, 'gradient_background_image'),
        ];
    }

    /**
     * Returns intro split section CMS content as a plain array.
     */
    private function getIntroSplitData(): array
    {
        $cms     = $this->getCmsSection(self::SECTION_INTRO_SPLIT);
        $heading = $this->text($cms, 'intro_heading');
        $closing = $this->text($cms, 'intro_closing');

        return [
            'heading'     => $heading,
            'body'        => $this->text($cms, 'intro_body'),
            'image'       => $this->imagePath($cms, 'intro_image'),
            'imageAlt'    => $this->text($cms, 'intro_image_alt', $heading),
            'subsections' => $this->buildIntroSubsections($cms),
            'closingLine' => $closing !== '' ? $closing : null,
        ];
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

    /**
     * Returns intro split 2 (reversed) section CMS content as a plain array.
     * Returns null if the section has no CMS content.
     */
    private function getIntroSplit2Data(): ?array
    {
        $cms = $this->getCmsSection(self::SECTION_INTRO_SPLIT2);

        if ($cms === []) {
            return null;
        }

        $heading = $this->text($cms, 'intro2_heading');

        return [
            'heading'  => $heading,
            'body'     => $this->text($cms, 'intro2_body'),
            'image'    => $this->imagePath($cms, 'intro2_image'),
            'imageAlt' => $this->text($cms, 'intro2_image_alt', $heading),
        ];
    }

    /**
     * Returns instructions section CMS content as a plain array.
     * Returns null if the section has no CMS content.
     */
    private function getInstructionsData(): ?array
    {
        $cms = $this->getCmsSection(self::SECTION_INSTRUCTIONS);

        if ($cms === []) {
            return null;
        }

        return [
            'title' => $this->text($cms, 'instructions_title'),
            'cards' => [
                ['number' => '1', 'title' => $this->text($cms, 'instructions_card_1_title'), 'text' => $this->text($cms, 'instructions_card_1_text'), 'icon' => 'search'],
                ['number' => '2', 'title' => $this->text($cms, 'instructions_card_2_title'), 'text' => $this->text($cms, 'instructions_card_2_text'), 'icon' => 'calendar'],
                ['number' => '3', 'title' => $this->text($cms, 'instructions_card_3_title'), 'text' => $this->text($cms, 'instructions_card_3_text'), 'icon' => 'check'],
            ],
        ];
    }

    // =====================================================================
    //  DOMAIN-DRIVEN SECTION — restaurant cards from the Restaurant table
    //  CMS only provides the section title + subtitle.
    //  The actual card data comes from the Restaurant DB table
    //  (joined with MediaAsset for images).
    // =====================================================================

    /**
     * Returns just the CMS title + subtitle for the restaurant cards section.
     * The actual card data comes from the domain (restaurants + cuisineFilters + cards).
     */
    private function getCardsSectionData(): array
    {
        $cms = $this->getCmsSection(self::SECTION_CARDS);

        return [
            'title'    => $this->text($cms, 'cards_title'),
            'subtitle' => $this->text($cms, 'cards_subtitle'),
        ];
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
     * Converts Restaurant domain models into plain card arrays for the mapper.
     *
     * @param  \App\Models\Restaurant[] $restaurants
     * @return array[]
     */
    private function buildCards(array $restaurants): array
    {
        $cards = [];

        foreach ($restaurants as $restaurant) {
            $cards[] = [
                'id'          => $restaurant->restaurantId,
                'name'        => $restaurant->name,
                'cuisine'     => $restaurant->cuisineType,
                'address'     => $this->buildAddress($restaurant),
                'description' => $this->cleanDescription($restaurant->descriptionHtml),
                'rating'      => $restaurant->stars ?? 0,
                'image'       => $restaurant->imagePath ?? self::DEFAULT_IMAGE,
            ];
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
