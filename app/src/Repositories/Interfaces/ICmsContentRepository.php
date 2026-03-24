<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

/**
 * Defines read-only queries for retrieving resolved CMS content by page and section.
 */
interface ICmsContentRepository
{
    /**
     * @return array<string, array<string, ?string>> CMS content keyed by section key, then item key
     */
    public function getHomePageContent(): array;

    /**
     * @return array<string, ?string> CMS item values keyed by item key
     */
    public function getSectionContent(string $pageSlug, string $sectionKey): array;

    /**
     * @return array<string, ?string> Hero section CMS item values keyed by item key
     */
    public function getHeroSectionContent(string $pageSlug): array;
}
