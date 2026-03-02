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
 * Service for preparing restaurant page data.
 *
 * CMS provides page copy (hero, gradient, intro, instructions titles).
 * Domain (Restaurant table) provides restaurant cards and cuisine filters.
 */
class RestaurantService implements IRestaurantService
{
    private const PAGE_SLUG = 'restaurant';

    private const SECTION_GRADIENT = 'gradient_section';
    private const SECTION_INTRO_SPLIT = 'intro_split_section';
    private const SECTION_INTRO_SPLIT2 = 'intro_split2_section';
    private const SECTION_INSTRUCTIONS = 'instructions_section';
    private const SECTION_CARDS = 'restaurant_cards_section';

    private const DEFAULT_IMAGE = '/assets/Image/Image (Yummy).png';
    private const VALID_IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif'];

    private const DEFAULT_CARDS_TITLE = 'Explore the participant restaurants';
    private const DEFAULT_CARDS_SUBTITLE = 'Discover all restaurants participating in Yummy! Each one offers a special festival menu, unique flavors, and limited time slots throughout the weekend.';

    private CmsService $cmsService;
    private RestaurantRepository $restaurantRepository;

    public function __construct()
    {
        $this->cmsService = new CmsService();
        $this->restaurantRepository = new RestaurantRepository();
    }

    /**
     * Builds the complete page ViewModel consumed by the restaurant view.
     */
    public function getRestaurantPageData(): RestaurantPageViewModel
    {
        return new RestaurantPageViewModel(
            heroData: $this->cmsService->buildHeroData(self::PAGE_SLUG, self::PAGE_SLUG),
            globalUi: $this->cmsService->buildGlobalUiData(),
            gradientSection: $this->buildGradientSection(),
            introSplitSection: $this->buildIntroSplitSection(),
            introSplit2Section: $this->buildIntroSplit2Section(),
            instructionsSection: $this->buildInstructionsSection(),
            restaurantCardsSection: $this->buildRestaurantCardsSection(),
        );
    }

    // ── CMS section builders ────────────────────────────────────────────

    private function buildGradientSection(): GradientSectionData
    {
        $content = $this->getSectionContent(self::SECTION_GRADIENT);

        return new GradientSectionData(
            headingText: $this->val($content, 'gradient_heading'),
            subheadingText: $this->val($content, 'gradient_subheading'),
            backgroundImageUrl: $this->imagePath($content, 'gradient_background_image'),
        );
    }

    private function buildIntroSplitSection(): IntroSplitSectionData
    {
        $content = $this->getSectionContent(self::SECTION_INTRO_SPLIT);

        $heading = $this->val($content, 'intro_heading');
        $parsed = $this->parseIntroBody($this->val($content, 'intro_body'));

        return new IntroSplitSectionData(
            headingText: $heading,
            bodyText: $parsed['bodyText'],
            imageUrl: $this->imagePath($content, 'intro_image'),
            imageAltText: $this->val($content, 'intro_image_alt', $heading),
            subsections: $parsed['subsections'],
            closingLine: $parsed['closingLine'],
        );
    }

    private function buildIntroSplit2Section(): ?IntroSplitSectionData
    {
        $content = $this->getSectionContent(self::SECTION_INTRO_SPLIT2);

        if ($content === []) {
            return null;
        }

        $heading = $this->val($content, 'intro2_heading');

        return new IntroSplitSectionData(
            headingText: $heading,
            bodyText: $this->val($content, 'intro2_body'),
            imageUrl: $this->imagePath($content, 'intro2_image'),
            imageAltText: $this->val($content, 'intro2_image_alt', $heading),
        );
    }

    private function buildInstructionsSection(): ?InstructionsSectionData
    {
        $content = $this->getSectionContent(self::SECTION_INSTRUCTIONS);

        if ($content === []) {
            return null;
        }

        return new InstructionsSectionData(
            title: $this->val($content, 'instructions_title'),
            cards: [
                new InstructionCardData('1', $this->val($content, 'instructions_card_1_title'), $this->val($content, 'instructions_card_1_text'), 'search'),
                new InstructionCardData('2', $this->val($content, 'instructions_card_2_title'), $this->val($content, 'instructions_card_2_text'), 'calendar'),
                new InstructionCardData('3', $this->val($content, 'instructions_card_3_title'), $this->val($content, 'instructions_card_3_text'), 'check'),
            ],
        );
    }

    // ── Domain-driven cards section ─────────────────────────────────────

    private function buildRestaurantCardsSection(): RestaurantCardsSectionData
    {
        $cmsCopy = $this->getSectionContent(self::SECTION_CARDS);
        $restaurants = $this->restaurantRepository->findAllActive();

        return new RestaurantCardsSectionData(
            title: $this->val($cmsCopy, 'cards_title', self::DEFAULT_CARDS_TITLE),
            subtitle: $this->val($cmsCopy, 'cards_subtitle', self::DEFAULT_CARDS_SUBTITLE),
            filters: $this->buildCuisineFilters($restaurants),
            cards: $this->buildCards($restaurants),
        );
    }

    /**
     * Derives unique cuisine filter labels from the Restaurant domain data.
     *
     * @param \App\Models\Restaurant[] $restaurants
     * @return string[]
     */
    private function buildCuisineFilters(array $restaurants): array
    {
        $seen = [];

        foreach ($restaurants as $restaurant) {
            foreach (array_filter(array_map('trim', explode(',', $restaurant->cuisineType))) as $cuisine) {
                $key = mb_strtolower($cuisine);
                if ($key !== '' && !isset($seen[$key])) {
                    $seen[$key] = $cuisine;
                }
            }
        }

        $labels = array_values($seen);
        sort($labels, SORT_NATURAL | SORT_FLAG_CASE);

        return ['All', ...$labels];
    }

    /**
     * Maps domain Restaurant models to card ViewModels.
     *
     * @param \App\Models\Restaurant[] $restaurants
     * @return RestaurantCardData[]
     */
    private function buildCards(array $restaurants): array
    {
        $cards = [];

        foreach ($restaurants as $restaurant) {
            $cards[] = new RestaurantCardData(
                name: $restaurant->name,
                cuisine: $restaurant->cuisineType,
                address: $this->formatAddress($restaurant->addressLine, $restaurant->city),
                description: $this->htmlToPlainText($restaurant->descriptionHtml),
                rating: $restaurant->stars ?? 0,
                image: $this->resolveCardImage($restaurant->name),
            );
        }

        return $cards;
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    /**
     * Shorthand for fetching a CMS section for this page.
     */
    private function getSectionContent(string $sectionKey): array
    {
        return $this->cmsService->getSectionContent(self::PAGE_SLUG, $sectionKey);
    }

    /**
     * Returns a non-empty string value from CMS content or a default.
     */
    private function val(array $content, string $key, string $default = ''): string
    {
        $value = $content[$key] ?? null;
        return is_string($value) && $value !== '' ? $value : $default;
    }

    /**
     * Validates and returns an image path from CMS content.
     */
    private function imagePath(array $content, string $key): string
    {
        return $this->validateImagePath((string)($content[$key] ?? ''));
    }

    private function validateImagePath(string $path): string
    {
        if ($path === '' || !str_starts_with($path, '/assets/')) {
            return self::DEFAULT_IMAGE;
        }

        return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), self::VALID_IMAGE_EXTENSIONS, true)
            ? $path
            : self::DEFAULT_IMAGE;
    }

    private function formatAddress(string $addressLine, string $city): string
    {
        return trim($addressLine . ($city !== '' ? ', ' . $city : ''));
    }

    /**
     * Strips HTML tags and normalises whitespace for card descriptions.
     */
    private function htmlToPlainText(string $html): string
    {
        $html = trim($html);
        if ($html === '' || $html === '<p></p>') {
            return '';
        }

        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return trim(preg_replace('/\s+/', ' ', $text) ?? $text);
    }

    /**
     * Resolves a card image path from the restaurant name.
     *
     * Convention: /assets/Image/restaurants/Restaurant-<Slug>-card.(png|jpg)
     */
    private function resolveCardImage(string $name): string
    {
        $slug = $this->sanitiseName($name);
        if ($slug === '') {
            return self::DEFAULT_IMAGE;
        }

        $base = '/assets/Image/restaurants/Restaurant-' . $slug . '-card';
        $publicRoot = __DIR__ . '/../../public';

        if (is_file($publicRoot . $base . '.png')) {
            return $base . '.png';
        }
        if (is_file($publicRoot . $base . '.jpg')) {
            return $base . '.jpg';
        }

        return self::DEFAULT_IMAGE;
    }

    /**
     * Converts a restaurant name to a safe filename fragment.
     * "Café de Roemer" → "CafeDeRoemer"
     */
    private function sanitiseName(string $name): string
    {
        $name = trim($name);
        if ($name === '') {
            return '';
        }

        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT', $name);
        if ($ascii !== false) {
            $name = $ascii;
        }

        return preg_replace('/[^A-Za-z0-9]+/', '', $name) ?? '';
    }

    /**
     * Parses an intro body blob with ## subsection markers.
     *
     * @return array{bodyText: string, subsections: ?array, closingLine: ?string}
     */
    private function parseIntroBody(string $raw): array
    {
        $result = ['bodyText' => '', 'subsections' => null, 'closingLine' => null];
        $raw = trim(str_replace(["\r\n", "\r"], "\n", $raw));

        if ($raw === '') {
            return $result;
        }

        $blocks = preg_split("/\n\n+/", $raw);
        if ($blocks === false || $blocks === []) {
            return ['bodyText' => $raw] + $result;
        }

        $bodyParts = [];
        $subsections = [];
        $i = 0;

        for (; $i < count($blocks); $i++) {
            $b = trim($blocks[$i]);
            if (str_starts_with($b, '## ')) {
                break;
            }
            if ($b !== '') {
                $bodyParts[] = $b;
            }
        }
        $result['bodyText'] = implode("\n\n", $bodyParts);

        for (; $i < count($blocks); $i++) {
            $b = trim($blocks[$i]);
            if ($b === '') {
                continue;
            }

            if (str_starts_with($b, '## ')) {
                $text = '';
                if (($i + 1) < count($blocks)) {
                    $next = trim($blocks[$i + 1]);
                    if ($next !== '' && !str_starts_with($next, '## ')) {
                        $text = $next;
                        $i++;
                    }
                }
                $subsections[] = ['heading' => trim(substr($b, 3)), 'text' => $text];
                continue;
            }

            $result['closingLine'] = $b;
        }

        if ($subsections !== []) {
            $result['subsections'] = $subsections;
        }

        return $result;
    }
}
