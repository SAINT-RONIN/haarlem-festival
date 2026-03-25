<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\StorytellingContentMapper;
use App\Models\StorytellingEventCmsData;
use App\Models\StorytellingMasonrySectionContent;
use App\Repositories\Interfaces\ICmsContentRepository;

/**
 * Provides typed access to Storytelling page CMS content sections.
 *
 * Wraps the generic ICmsContentRepository and delegates field mapping
 * to StorytellingContentMapper.
 */
class StorytellingContentRepository
{
    public function __construct(
        private readonly ICmsContentRepository $cmsContent,
    ) {
    }

    /** Fetches the storytelling masonry section content. */
    public function findMasonryContent(string $pageSlug, string $sectionKey): StorytellingMasonrySectionContent
    {
        $raw = $this->cmsContent->getSectionContent($pageSlug, $sectionKey);
        return StorytellingContentMapper::mapMasonry($raw);
    }

    /** Fetches the storytelling event CMS data for a specific event section. */
    public function findEventCmsData(string $pageSlug, string $sectionKey): StorytellingEventCmsData
    {
        $raw = $this->cmsContent->getSectionContent($pageSlug, $sectionKey);
        return StorytellingContentMapper::mapEventCmsData($raw);
    }
}
