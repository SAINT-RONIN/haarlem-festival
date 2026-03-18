<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Interfaces\ICmsContentRepository;
use App\Services\Interfaces\ICmsPageContentService;

class CmsPageContentService implements ICmsPageContentService
{
    public function __construct(private ICmsContentRepository $repository) {}

    public function getSectionContent(string $pageSlug, string $sectionKey): array
    {
        return $this->repository->getSectionContent($pageSlug, $sectionKey);
    }
}
