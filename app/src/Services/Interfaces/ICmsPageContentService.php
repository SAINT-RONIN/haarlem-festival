<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

/**
 * Defines the contract for retrieving CMS page content by page slug and section key.
 */
interface ICmsPageContentService
{
    /**
     * Returns CMS item values for a specific section of a page, keyed by item key.
     *
     * @return array<string, ?string>
     */
    public function getSectionContent(string $pageSlug, string $sectionKey): array;
}
