<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\StorytellingContentMapper;
use App\DTOs\Cms\StorytellingEventCmsData;
use App\DTOs\Cms\StorytellingMasonrySectionContent;

/**
 * Provides typed access to Storytelling page CMS content sections.
 *
 * Wraps the generic ICmsContentRepository and delegates field mapping
 * to StorytellingContentMapper.
 */
class StorytellingContentRepository extends BaseContentRepository implements Interfaces\IStorytellingContentRepository
{
    /** Fetches the storytelling masonry section content. */
    public function findMasonryContent(string $pageSlug, string $sectionKey): StorytellingMasonrySectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return StorytellingContentMapper::mapMasonry($raw);
    }

    /** Fetches the storytelling event CMS data for a specific event section. */
    public function findEventCmsData(string $pageSlug, string $sectionKey): StorytellingEventCmsData
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return StorytellingContentMapper::mapEventCmsData($raw);
    }
}
