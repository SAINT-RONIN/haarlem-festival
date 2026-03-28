<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Content\StorytellingEventCmsData;
use App\Content\StorytellingMasonrySectionContent;

/**
 * Typed access to Storytelling page CMS content sections.
 */
interface IStorytellingContentRepository
{
    /** Fetches the storytelling masonry section content. */
    public function findMasonryContent(string $pageSlug, string $sectionKey): StorytellingMasonrySectionContent;

    /** Fetches the storytelling event CMS data for a specific event section. */
    public function findEventCmsData(string $pageSlug, string $sectionKey): StorytellingEventCmsData;
}
