<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\CmsRepository;
use App\ViewModels\GlobalUiData;
use App\ViewModels\HeroData;

class CmsService
{
    private CmsRepository $cmsRepository;

    private const DEFAULT_HERO_IMAGE = '/assets/Image/HeroImageHome.png';
    private const VALID_IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg', 'webp', 'gif'];

    public function __construct()
    {
        $this->cmsRepository = new CmsRepository();
    }

    /**
     * Builds a fully-populated HeroData DTO for the given page.
     * Applies defaults and validates the background image path.
     */
    public function buildHeroData(string $pageSlug, string $currentPage): HeroData
    {
        $heroContent = $this->getSectionContent($pageSlug, 'hero_section');

        return new HeroData(
            mainTitle: $this->getStringValue($heroContent, 'hero_main_title', 'Haarlem Festival'),
            subtitle: $this->getStringValue($heroContent, 'hero_subtitle', ''),
            primaryButtonText: $this->getStringValue($heroContent, 'hero_button_primary', 'Explore'),
            primaryButtonLink: $this->getStringValue($heroContent, 'hero_button_primary_link', '#events'),
            secondaryButtonText: $this->getStringValue($heroContent, 'hero_button_secondary', 'Schedule'),
            secondaryButtonLink: $this->getStringValue($heroContent, 'hero_button_secondary_link', '#schedule'),
            backgroundImageUrl: $this->validateImagePath($heroContent['hero_background_image'] ?? ''),
            currentPage: $currentPage,
        );
    }

    /**
     * Builds HeroData with a specific background image override.
     * Used when the service layer controls the image path directly.
     */
    public function buildHeroDataWithImage(string $pageSlug, string $currentPage, string $backgroundImageUrl): HeroData
    {
        $heroContent = $this->getSectionContent($pageSlug, 'hero_section');

        return new HeroData(
            mainTitle: $this->getStringValue($heroContent, 'hero_main_title', 'Haarlem Festival'),
            subtitle: $this->getStringValue($heroContent, 'hero_subtitle', ''),
            primaryButtonText: $this->getStringValue($heroContent, 'hero_button_primary', 'Explore'),
            primaryButtonLink: $this->getStringValue($heroContent, 'hero_button_primary_link', '#events'),
            secondaryButtonText: $this->getStringValue($heroContent, 'hero_button_secondary', 'Schedule'),
            secondaryButtonLink: $this->getStringValue($heroContent, 'hero_button_secondary_link', '#schedule'),
            backgroundImageUrl: $backgroundImageUrl,
            currentPage: $currentPage,
        );
    }

    /**
     * Builds a fully-populated GlobalUiData DTO.
     * Fetches from homepage global_ui section (shared across all pages).
     */
    public function buildGlobalUiData(): GlobalUiData
    {
        $globalContent = $this->getSectionContent('home', 'global_ui');

        return new GlobalUiData(
            siteName: $this->getStringValue($globalContent, 'site_name', 'Haarlem Festival'),
            navHome: $this->getStringValue($globalContent, 'nav_home', 'Home'),
            navJazz: $this->getStringValue($globalContent, 'nav_jazz', 'Jazz'),
            navDance: $this->getStringValue($globalContent, 'nav_dance', 'Dance'),
            navHistory: $this->getStringValue($globalContent, 'nav_history', 'History'),
            navRestaurant: $this->getStringValue($globalContent, 'nav_restaurant', 'Restaurant'),
            navStorytelling: $this->getStringValue($globalContent, 'nav_storytelling', 'Storytelling'),
            btnMyProgram: $this->getStringValue($globalContent, 'btn_my_program', 'My Program'),
        );
    }

    /**
     * Validates an image path. Returns default if invalid.
     */
    private function validateImagePath(string $path): string
    {
        if (empty($path)) {
            return self::DEFAULT_HERO_IMAGE;
        }

        if (!str_starts_with($path, '/assets/')) {
            return self::DEFAULT_HERO_IMAGE;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (!in_array($extension, self::VALID_IMAGE_EXTENSIONS, true)) {
            return self::DEFAULT_HERO_IMAGE;
        }

        return $path;
    }

    /**
     * Gets a string value from content array with default fallback.
     */
    private function getStringValue(array $content, string $key, string $default): string
    {
        $value = $content[$key] ?? null;
        return is_string($value) && $value !== '' ? $value : $default;
    }

    /**
     * Gets all CMS content for a page, keyed by section then item.
     */
    public function getPageContent(string $slug): array
    {
        $page = $this->cmsRepository->getPageBySlug($slug);
        if (!$page) {
            return [];
        }

        return $this->buildPageContentArray((int)$page['CmsPageId']);
    }

    public function getHomePageContent(): array
    {
        $page = $this->cmsRepository->getPageBySlug('home');
        if (!$page) {
            return [];
        }

        return $this->buildPageContentArray((int)$page['CmsPageId']);
    }

    private function buildPageContentArray(int $cmsPageId): array
    {
        $sections = $this->cmsRepository->getSectionsByPageId($cmsPageId);
        $content = [];

        foreach ($sections as $section) {
            $items = $this->cmsRepository->getItemsBySectionId((int)$section['CmsSectionId']);
            $sectionData = [];

            foreach ($items as $item) {
                $value = $item['TextValue'] ?? $item['HtmlValue'] ?? null;
                $sectionData[$item['ItemKey']] = $value;
            }

            $content[$section['SectionKey']] = $sectionData;
        }

        return $content;
    }

    public function getSectionContent(string $pageSlug, string $sectionKey): array
    {
        $page = $this->cmsRepository->getPageBySlug($pageSlug);
        if (!$page) {
            return [];
        }

        $items = $this->cmsRepository->getItemsBySectionKey((int)$page['CmsPageId'], $sectionKey);
        $content = [];

        foreach ($items as $item) {
            $value = $item['TextValue'] ?? $item['HtmlValue'] ?? null;
            $content[$item['ItemKey']] = $value;
        }

        return $content;
    }
}
