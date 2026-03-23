<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Interfaces\ICmsContentRepository;
use App\Services\Interfaces\ICmsPageContentService;

/**
 * Thin pass-through service for reading a single CMS section's key-value content.
 *
 * Exists so controllers depend on a service interface rather than
 * calling the CMS content repository directly.
 */
class CmsPageContentService implements ICmsPageContentService
{
    public function __construct(private ICmsContentRepository $repository) {}

    /**
     * Returns the key-value pairs for a single CMS section (e.g. "hero_section" on the "jazz" page).
     *
     * @return array<string, ?string> item-key => text/html value
     */
    public function getSectionContent(string $pageSlug, string $sectionKey): array
    {
        return $this->repository->getSectionContent($pageSlug, $sectionKey);
    }
}
