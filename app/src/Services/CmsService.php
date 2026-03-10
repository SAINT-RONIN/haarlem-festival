<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CmsItem;
use App\Models\CmsSection;
use App\Repositories\CmsRepository;
use App\Repositories\MediaAssetRepository;
use App\Services\Interfaces\ICmsService;
use App\ViewModels\GlobalUiData;
use App\ViewModels\HeroData;

class CmsService implements ICmsService
{
    private CmsRepository $cmsRepository;
    private MediaAssetRepository $mediaAssetRepository;
    private SessionService $sessionService;

    // Default values for Global UI
    private const DEFAULT_SITE_NAME = 'Haarlem Festivals';
    private const DEFAULT_NAV_HOME = 'Home';
    private const DEFAULT_NAV_JAZZ = 'Jazz';
    private const DEFAULT_NAV_DANCE = 'Dance';
    private const DEFAULT_NAV_HISTORY = 'History';
    private const DEFAULT_NAV_RESTAURANT = 'Restaurant';
    private const DEFAULT_NAV_STORYTELLING = 'Storytelling';
    private const DEFAULT_BTN_MY_PROGRAM = 'My Program';
    private const DEFAULT_LOGIN_LABEL = 'Login';
    private const DEFAULT_LOGOUT_LABEL = 'Logout';

    // Default values for Hero section
    private const DEFAULT_HERO_TITLE = 'Welcome';
    private const DEFAULT_HERO_SUBTITLE = '';
    private const DEFAULT_HERO_BUTTON_PRIMARY = 'Explore';
    private const DEFAULT_HERO_BUTTON_SECONDARY = 'Learn More';
    private const DEFAULT_HERO_LINK_PRIMARY = '#';
    private const DEFAULT_HERO_LINK_SECONDARY = '#';
    private const DEFAULT_HERO_IMAGE = '/assets/Image/HeroImageHome.png';

    public function __construct()
    {
        $this->cmsRepository = new CmsRepository();
        $this->mediaAssetRepository = new MediaAssetRepository();
        $this->sessionService = new SessionService();
    }

    public function getHomePageContent(): array
    {
        $pageId = $this->getPageIdBySlug('home');
        if ($pageId === null) {
            return [];
        }

        $sections = $this->cmsRepository->findSections(['cmsPageId' => $pageId]);
        $items = $this->cmsRepository->findItems(['cmsPageId' => $pageId]);
        $itemsBySection = $this->indexItemsBySectionId($items);
        $content = [];

        foreach ($sections as $section) {
            /** @var CmsSection $section */
            $sectionData = [];

            foreach ($itemsBySection[$section->cmsSectionId] ?? [] as $item) {
                /** @var CmsItem $item */
                $sectionData[$item->itemKey] = $this->resolveItemValue($item);
            }

            $content[$section->sectionKey] = $sectionData;
        }

        return $content;
    }

    public function getSectionContent(string $pageSlug, string $sectionKey): array
    {
        $pageId = $this->getPageIdBySlug($pageSlug);
        if ($pageId === null) {
            return [];
        }

        $items = $this->cmsRepository->findItems([
            'cmsPageId' => $pageId,
            'sectionKey' => $sectionKey,
        ]);
        $content = [];

        foreach ($items as $item) {
            /** @var CmsItem $item */
            $content[$item->itemKey] = $this->resolveItemValue($item);
        }

        return $content;
    }

    /**
     * Resolves the value to expose to views for a CMS item.
     *
     * - If the item references a MediaAsset, returns its FilePath.
     * - Otherwise returns TextValue or HtmlValue.
     */
    private function resolveItemValue(CmsItem $item): ?string
    {
        $mediaAssetId = $item->mediaAssetId;
        if ($mediaAssetId !== null && $mediaAssetId > 0) {
            $asset = $this->mediaAssetRepository->findById($mediaAssetId);
            if ($asset !== null && $asset->filePath !== '') {
                return $asset->filePath;
            }
        }

        $value = $item->textValue ?? $item->htmlValue ?? null;
        return is_string($value) ? $value : null;
    }

    private function getPageIdBySlug(string $slug): ?int
    {
        $rows = $this->cmsRepository->findPages(['slug' => $slug]);
        if ($rows === []) {
            return null;
        }

        return (int)$rows[0]['CmsPageId'];
    }

    /**
     * @param CmsItem[] $items
     * @return array<int, list<CmsItem>>
     */
    private function indexItemsBySectionId(array $items): array
    {
        $indexed = [];
        foreach ($items as $item) {
            $indexed[$item->cmsSectionId][] = $item;
        }

        return $indexed;
    }

    /**
     * Builds HeroData from CMS content for a given page.
     * Uses the CMS hero_section content and falls back to defaults.
     */
    public function buildHeroData(string $pageSlug, string $currentPage): HeroData
    {
        $heroContent = $this->getSectionContent($pageSlug, 'hero_section');

        return new HeroData(
            mainTitle: $heroContent['hero_main_title'] ?? self::DEFAULT_HERO_TITLE,
            subtitle: $heroContent['hero_subtitle'] ?? self::DEFAULT_HERO_SUBTITLE,
            primaryButtonText: $heroContent['hero_button_primary'] ?? self::DEFAULT_HERO_BUTTON_PRIMARY,
            primaryButtonLink: $heroContent['hero_button_primary_link'] ?? self::DEFAULT_HERO_LINK_PRIMARY,
            secondaryButtonText: $heroContent['hero_button_secondary'] ?? self::DEFAULT_HERO_BUTTON_SECONDARY,
            secondaryButtonLink: $heroContent['hero_button_secondary_link'] ?? self::DEFAULT_HERO_LINK_SECONDARY,
            backgroundImageUrl: $heroContent['hero_background_image'] ?? self::DEFAULT_HERO_IMAGE,
            currentPage: $currentPage,
        );
    }

    /**
     * Builds HeroData from CMS content with a custom background image override.
     * Useful for pages that have hardcoded images instead of CMS-managed ones.
     */
    public function buildHeroDataWithImage(string $pageSlug, string $currentPage, string $backgroundImageUrl): HeroData
    {
        $heroContent = $this->getSectionContent($pageSlug, 'hero_section');

        return new HeroData(
            mainTitle: $heroContent['hero_main_title'] ?? self::DEFAULT_HERO_TITLE,
            subtitle: $heroContent['hero_subtitle'] ?? self::DEFAULT_HERO_SUBTITLE,
            primaryButtonText: $heroContent['hero_button_primary'] ?? self::DEFAULT_HERO_BUTTON_PRIMARY,
            primaryButtonLink: $heroContent['hero_button_primary_link'] ?? self::DEFAULT_HERO_LINK_PRIMARY,
            secondaryButtonText: $heroContent['hero_button_secondary'] ?? self::DEFAULT_HERO_BUTTON_SECONDARY,
            secondaryButtonLink: $heroContent['hero_button_secondary_link'] ?? self::DEFAULT_HERO_LINK_SECONDARY,
            backgroundImageUrl: $backgroundImageUrl,
            currentPage: $currentPage,
        );
    }

    /**
     * Builds GlobalUiData from CMS content.
     * Uses the global_ui section or falls back to defaults.
     */
    public function buildGlobalUiData(): GlobalUiData
    {
        // Try to get global UI content from a dedicated section or use defaults
        $globalContent = $this->getSectionContent('home', 'global_ui');

        return new GlobalUiData(
            siteName: $globalContent['site_name'] ?? self::DEFAULT_SITE_NAME,
            navHome: $globalContent['nav_home'] ?? self::DEFAULT_NAV_HOME,
            navJazz: $globalContent['nav_jazz'] ?? self::DEFAULT_NAV_JAZZ,
            navDance: $globalContent['nav_dance'] ?? self::DEFAULT_NAV_DANCE,
            navHistory: $globalContent['nav_history'] ?? self::DEFAULT_NAV_HISTORY,
            navRestaurant: $globalContent['nav_restaurant'] ?? self::DEFAULT_NAV_RESTAURANT,
            navStorytelling: $globalContent['nav_storytelling'] ?? self::DEFAULT_NAV_STORYTELLING,
            btnMyProgram: $globalContent['btn_my_program'] ?? self::DEFAULT_BTN_MY_PROGRAM,
            loginLabel: $globalContent['login_label'] ?? self::DEFAULT_LOGIN_LABEL,
            logoutLabel: $globalContent['logout_label'] ?? self::DEFAULT_LOGOUT_LABEL,
            isLoggedIn: $this->sessionService->isLoggedIn(),
        );
    }
}
