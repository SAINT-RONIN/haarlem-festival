<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\ViewModels\GlobalUiData;
use App\ViewModels\HeroData;

/**
 * Interface for CMS content service.
 */
interface ICmsService
{
    /**
     * Gets home page CMS content.
     *
     * @return array Structured content by section
     */
    public function getHomePageContent(): array;

    /**
     * Gets content for a specific section on a page.
     *
     * @param string $pageSlug Page slug
     * @param string $sectionKey Section key
     * @return array Section content
     */
    public function getSectionContent(string $pageSlug, string $sectionKey): array;

    /**
     * Builds hero section data from CMS content.
     *
     * @param string $pageSlug Page slug
     * @param string $currentPage Current page identifier
     * @return HeroData Hero section data
     */
    public function buildHeroData(string $pageSlug, string $currentPage): HeroData;

    /**
     * Builds global UI data from CMS content.
     *
     * @return GlobalUiData Global UI data
     */
    public function buildGlobalUiData(): GlobalUiData;
}

