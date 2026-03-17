<?php

declare(strict_types=1);

namespace App\Services;

use App\Mappers\CmsMapper;
use App\ViewModels\GlobalUiData;
use App\ViewModels\GradientSectionData;
use App\ViewModels\IntroSplitSectionData;
use App\ViewModels\Restaurant\RestaurantPageViewModel;

/**
 * Service for preparing restaurant page data.
 *
 * Assembles all data needed for the restaurant page view.
 */
class RestaurantPageService
{
    private const DEFAULT_IMAGE = '/assets/Image/Image (Yummy).png';
    private const VALID_IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif'];

    public function __construct(
        private CmsService $cmsService,
    ) {
    }

    private function isDbOnly(): bool
    {
        $val = $_GET['source'] ?? '';
        return is_string($val) && strtolower($val) === 'db';
    }

    private function isDev(): bool
    {
        $env = $_ENV['APP_ENV'] ?? '';
        return is_string($env) && strtolower($env) === 'dev';
    }

    /**
     * Applies fallback only in dev mode and only when not using ?source=db.
     */
    private function applyDevFallback(array $cms, array $fallback, bool $dbOnly): array
    {
        if ($dbOnly || !$this->isDev()) {
            return $cms;
        }

        $merged = $cms;
        foreach ($fallback as $key => $value) {
            $existing = $merged[$key] ?? null;
            if (!is_string($existing) || trim($existing) === '') {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Hardcoded fallback values (dev-only safety net).
     */
    private function getHardcodedFallback(): array
    {
        return [
            'hero_section' => [
                'hero_main_title' => 'Yummy Gourmet with a Twist',
                'hero_subtitle' => "Discover 7 gourmet restaurants offering exclusive festival\nmenus crafted by top local chefs.",
                'hero_button_primary' => 'Discover restaurants',
                'hero_button_primary_link' => '#restaurants',
                'hero_button_secondary' => 'About Yummy',
                'hero_button_secondary_link' => '#about',
                'hero_background_image' => '/assets/Image/restaurants/hero-picture.png',
            ],
            'gradient_section' => [
                'gradient_heading' => 'Good food tastes better when shared.',
                'gradient_subheading' => 'Food, stories, and shared moments across Haarlem.',
                'gradient_background_image' => '/assets/Image/restaurants/chef-preparing-food.png',
            ],
            'intro_split_section' => [
                'intro_heading' => 'Yummy! at the Heart of the Festival',
                'intro_body' => "Welcome to Yummy!, the food experience of the Haarlem Festival.\nFour days where some of the city's favorite restaurants open their doors with special menus made just for this event.\n\n## What is Yummy?\nA festival of food where each restaurant offers one unique menu, set time slots, and special prices.\n\n## Who takes part?\nLocal chefs and restaurants from all around Haarlem, prepare with their own style a great variety of dishes, such as: Dutch-French-European-Fish & Seafood-Modern Vegan.\n\n## How does it work?\nChoose a restaurant, pick a time slot, and make a reservation. Seats are limited, so booking ahead is important.\n\nCome enjoy great food, good company, and a warm festival atmosphere.",
                'intro_image' => '/assets/Image/restaurants/table-with-food-and-drink.png',
                'intro_image_alt' => 'Yummy! at the Heart of the Festival',
            ],
            'intro_split2_section' => [
                'intro2_heading' => 'When Haarlem Becomes a Dining Room',
                'intro2_body' => "As the sun sets over Haarlem’s historic streets, the city slowly turns into one big dining room.\n\nFrom Thursday to Sunday, each restaurant offers 2 to 3 sessions later afternoon, starting from 16:30 and lasting around 1.5 to 2 hours..\n\nJust enough time to enjoy your plate, share a toast, and wander to the next event or performance nearby.",
                'intro2_image' => '/assets/Image/restaurants/food-in-canal.png',
                'intro2_image_alt' => 'When Haarlem Becomes a Dining Room',
            ],
            'instructions_section' => [
                'instructions_title' => 'How reservations work',
                'instructions_card_1_title' => 'Browse',
                'instructions_card_1_text' => 'Explore participating restaurants and their exclusive festival menus.',
                'instructions_card_2_title' => 'Choose',
                'instructions_card_2_text' => 'Pick a date and time slot that fits your schedule.',
                'instructions_card_3_title' => 'Reserve',
                'instructions_card_3_text' => 'Complete your booking and receive a confirmation. Done!',
            ],
            'restaurant_cards_section' => [
                'cards_title' => 'Explore the participant restaurants',
                'cards_subtitle' => 'Discover all restaurants participating in Yummy! Each one offers a special festival menu, unique flavors, and limited time slots throughout the weekend.',
                'filter_all' => 'All',
                'filter_dutch' => 'Dutch',
                'filter_european' => 'European',
                'filter_french' => 'French',
                'filter_modern' => 'Modern',
                'filter_fish_seafood' => 'Fish & Seafood',
                'filter_vegetarian' => 'Vegetarian',
            ],
        ];
    }

    /**
     * Builds the restaurant page view model with all required data.
     *
     * Default: DB-driven.
     * Dev safety fallback: only when APP_ENV=dev and NOT ?source=db.
     * Verification: ?source=db forces DB-only (no fallback).
     */
    public function getRestaurantPageData(bool $isLoggedIn): RestaurantPageViewModel
    {
        $dbOnly = $this->isDbOnly();
        $fallback = $this->getHardcodedFallback();

        $heroContent = $this->cmsService->getSectionContent('restaurant', 'hero_section');
        $heroContent = $this->applyDevFallback($heroContent, $fallback['hero_section'], $dbOnly);
        $heroData = new \App\ViewModels\HeroData(
            mainTitle: (string)($heroContent['hero_main_title'] ?? ''),
            subtitle: (string)($heroContent['hero_subtitle'] ?? ''),
            primaryButtonText: (string)($heroContent['hero_button_primary'] ?? ''),
            primaryButtonLink: (string)($heroContent['hero_button_primary_link'] ?? ''),
            secondaryButtonText: (string)($heroContent['hero_button_secondary'] ?? ''),
            secondaryButtonLink: (string)($heroContent['hero_button_secondary_link'] ?? ''),
            backgroundImageUrl: $this->validateImagePath((string)($heroContent['hero_background_image'] ?? '')),
            currentPage: 'restaurant',
        );

        $gradientSection = $this->buildGradientSection($fallback, $dbOnly);
        $introSplitSection = $this->buildIntroSplitSection($fallback, $dbOnly);
        $introSplit2Section = $this->buildIntroSplit2Section($fallback, $dbOnly);
        $instructionsSection = $this->buildInstructionsSection($fallback, $dbOnly);
        $restaurantCardsSection = $this->buildRestaurantCardsSection($fallback, $dbOnly);

        $globalUiContent = $this->cmsService->getSectionContent('home', 'global_ui');

        return new RestaurantPageViewModel(
            heroData: $heroData,
            globalUi: CmsMapper::toGlobalUiData($globalUiContent, $isLoggedIn),
            gradientSection: $gradientSection,
            introSplitSection: $introSplitSection,
            introSplit2Section: $introSplit2Section,
            instructionsSection: $instructionsSection,
            restaurantCardsSection: $restaurantCardsSection,
        );
    }

    private function buildGradientSection(array $fallback, bool $dbOnly): GradientSectionData
    {
        $content = $this->cmsService->getSectionContent('restaurant', 'gradient_section');
        $content = $this->applyDevFallback($content, $fallback['gradient_section'], $dbOnly);

        return new GradientSectionData(
            headingText: $this->getStringValue($content, 'gradient_heading', ''),
            subheadingText: $this->getStringValue($content, 'gradient_subheading', ''),
            backgroundImageUrl: $this->validateImagePath((string)($content['gradient_background_image'] ?? '')),
        );
    }

    /**
     * Builds the intro split section data with image from CMS.
     * Parses the intro_body blob into bodyText, subsections, and closingLine.
     */
    private function buildIntroSplitSection(array $fallback, bool $dbOnly): IntroSplitSectionData
    {
        $content = $this->cmsService->getSectionContent('restaurant', 'intro_split_section');
        $content = $this->applyDevFallback($content, $fallback['intro_split_section'], $dbOnly);

        $heading = $this->getStringValue($content, 'intro_heading', '');
        $rawBody = $this->getStringValue($content, 'intro_body', '');

        // Parse the blob into structured data
        $parsed = $this->parseIntroBody($rawBody);

        return new IntroSplitSectionData(
            headingText: $heading,
            bodyText: $parsed['bodyText'],
            imageUrl: $this->validateImagePath((string)($content['intro_image'] ?? '')),
            imageAltText: $this->getStringValue($content, 'intro_image_alt', $heading),
            subsections: $parsed['subsections'],
            closingLine: $parsed['closingLine'],
        );
    }

    /**
     * Builds the restaurant-only intro split 2 section.
     */
    private function buildIntroSplit2Section(array $fallback, bool $dbOnly): ?IntroSplitSectionData
    {
        $content = $this->cmsService->getSectionContent('restaurant', 'intro_split2_section');
        $content = $this->applyDevFallback($content, $fallback['intro_split2_section'], $dbOnly);

        if (empty($content)) {
            return null;
        }

        $heading = $this->getStringValue($content, 'intro2_heading', '');
        $body = $this->getStringValue($content, 'intro2_body', '');

        return new IntroSplitSectionData(
            headingText: $heading,
            bodyText: $body,
            imageUrl: $this->validateImagePath((string)($content['intro2_image'] ?? '')),
            imageAltText: $this->getStringValue($content, 'intro2_image_alt', $heading),
        );
    }

    /**
     * Builds the "How reservations work" section. Restaurant-only.
     *
     * Returns a simple array consumed by the existing partial.
     */
    private function buildInstructionsSection(array $fallback, bool $dbOnly): ?array
    {
        $content = $this->cmsService->getSectionContent('restaurant', 'instructions_section');
        $content = $this->applyDevFallback($content, $fallback['instructions_section'], $dbOnly);

        if (empty($content)) {
            return null;
        }

        return [
            'title' => $this->getStringValue($content, 'instructions_title', ''),
            'cards' => [
                [
                    'number' => '1',
                    'title' => $this->getStringValue($content, 'instructions_card_1_title', ''),
                    'text' => $this->getStringValue($content, 'instructions_card_1_text', ''),
                    'icon' => 'search',
                ],
                [
                    'number' => '2',
                    'title' => $this->getStringValue($content, 'instructions_card_2_title', ''),
                    'text' => $this->getStringValue($content, 'instructions_card_2_text', ''),
                    'icon' => 'calendar',
                ],
                [
                    'number' => '3',
                    'title' => $this->getStringValue($content, 'instructions_card_3_title', ''),
                    'text' => $this->getStringValue($content, 'instructions_card_3_text', ''),
                    'icon' => 'check',
                ],
            ],
        ];
    }

    /**
     * Builds the restaurant cards section.
     * Returns an array consumed by the existing restaurant-cards-section partial.
     */
    private function buildRestaurantCardsSection(array $fallback, bool $dbOnly): ?array
    {
        $content = $this->cmsService->getSectionContent('restaurant', 'restaurant_cards_section');
        $content = $this->applyDevFallback($content, $fallback['restaurant_cards_section'], $dbOnly);

        if (empty($content)) {
            return null;
        }

        $cards = [];
        for ($i = 1; $i <= 7; $i++) {
            $cards[] = [
                'name' => $this->getStringValue($content, "restaurant_{$i}_name", ''),
                'cuisine' => $this->getStringValue($content, "restaurant_{$i}_cuisine", ''),
                'address' => $this->getStringValue($content, "restaurant_{$i}_address", ''),
                'description' => $this->getStringValue($content, "restaurant_{$i}_description", ''),
                'distanceText' => $this->getStringValue($content, "restaurant_{$i}_distance_text", ''),
                'rating' => $this->getStringValue($content, "restaurant_{$i}_rating", ''),
                'price' => $this->getStringValue($content, "restaurant_{$i}_price", ''),
                'image' => $this->validateImagePath(
                    $this->getStringValue($content, "restaurant_{$i}_image", '')
                ),
                'aboutLabel' => $this->getStringValue($content, "restaurant_{$i}_about_label", ''),
                'bookLabel' => $this->getStringValue($content, "restaurant_{$i}_book_label", ''),
            ];
        }

        return [
            'title' => $this->getStringValue($content, 'cards_title', ''),
            'subtitle' => $this->getStringValue($content, 'cards_subtitle', ''),
            'filters' => [
                $this->getStringValue($content, 'filter_all', ''),
                $this->getStringValue($content, 'filter_dutch', ''),
                $this->getStringValue($content, 'filter_european', ''),
                $this->getStringValue($content, 'filter_french', ''),
                $this->getStringValue($content, 'filter_modern', ''),
                $this->getStringValue($content, 'filter_fish_seafood', ''),
                $this->getStringValue($content, 'filter_vegetarian', ''),
            ],
            'cards' => $cards,
        ];
    }

    /**
     * Parses the intro_body blob into structured components.
     *
     * Restaurant convention (matches our seeded content):
     * - First block before any "##" is bodyText
     * - Each "## Heading" becomes a subsection
     * - Final paragraph after the last subsection (without "##") becomes closingLine
     */
    private function parseIntroBody(string $rawBody): array
    {
        $result = [
            'bodyText' => '',
            'subsections' => null,
            'closingLine' => null,
        ];

        $rawBody = trim($rawBody);
        if ($rawBody === '') {
            return $result;
        }

        // Normalize line endings
        $rawBody = str_replace(["\r\n", "\r"], "\n", $rawBody);

        // Split into blocks separated by blank lines
        $blocks = preg_split("/\n\n+/", $rawBody);
        if ($blocks === false || $blocks === []) {
            $result['bodyText'] = $rawBody;
            return $result;
        }

        // First contiguous block(s) until the first ## is bodyText
        $bodyParts = [];
        $subsections = [];
        $i = 0;

        // Collect body blocks until we hit a subsection marker
        for (; $i < count($blocks); $i++) {
            $b = trim((string)$blocks[$i]);
            if (str_starts_with($b, '## ')) {
                break;
            }
            if ($b !== '') {
                $bodyParts[] = $b;
            }
        }
        $result['bodyText'] = implode("\n\n", $bodyParts);

        // Parse subsections
        for (; $i < count($blocks); $i++) {
            $b = trim((string)$blocks[$i]);
            if ($b === '') {
                continue;
            }

            if (str_starts_with($b, '## ')) {
                $heading = trim(substr($b, 3));
                $text = '';

                // Next block (if any) is the text for this subsection, unless it is another heading.
                if (($i + 1) < count($blocks)) {
                    $next = trim((string)$blocks[$i + 1]);
                    if ($next !== '' && !str_starts_with($next, '## ')) {
                        $text = $next;
                        $i++; // consume the text block
                    }
                }

                $subsections[] = [
                    'heading' => $heading,
                    'text' => $text,
                ];
                continue;
            }

            // Any remaining non-heading block becomes closingLine (last one wins)
            $result['closingLine'] = $b;
        }

        if ($subsections !== []) {
            $result['subsections'] = $subsections;
        }

        return $result;
    }

    /**
     * Validates an image path. Returns default if invalid.
     */
    private function validateImagePath(string $path): string
    {
        if (empty($path)) {
            return self::DEFAULT_IMAGE;
        }

        if (!str_starts_with($path, '/assets/')) {
            return self::DEFAULT_IMAGE;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($extension, self::VALID_IMAGE_EXTENSIONS, true)) {
            return self::DEFAULT_IMAGE;
        }

        return $path;
    }

    private function getStringValue(array $content, string $key, string $default): string
    {
        $value = $content[$key] ?? null;
        return is_string($value) && $value !== '' ? $value : $default;
    }
}
