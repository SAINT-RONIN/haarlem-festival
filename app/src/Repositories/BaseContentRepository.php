<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Repositories\Interfaces\ICmsContentRepository;

// Base for typed content repositories that delegate to ICmsContentRepository.
abstract class BaseContentRepository
{
    public function __construct(
        protected readonly ICmsContentRepository $cmsContent,
    ) {}

    protected function fetchSectionContent(string $pageSlug, string $sectionKey): array
    {
        return $this->cmsContent->getSectionContent($pageSlug, $sectionKey);
    }
}
