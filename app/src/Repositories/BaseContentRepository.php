<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Interfaces\ICmsContentRepository;

/**
 * Base class for typed content repositories that wrap ICmsContentRepository.
 *
 * Provides the shared constructor and a helper for fetching raw section data.
 * Concrete subclasses define typed methods that delegate to domain-specific mappers.
 */
abstract class BaseContentRepository
{
    public function __construct(
        protected readonly ICmsContentRepository $cmsContent,
    ) {
    }

    /**
     * Fetches raw CMS section content for a given page and section key.
     *
     * @return array<string, mixed>
     */
    protected function fetchSectionContent(string $pageSlug, string $sectionKey): array
    {
        return $this->cmsContent->getSectionContent($pageSlug, $sectionKey);
    }
}
