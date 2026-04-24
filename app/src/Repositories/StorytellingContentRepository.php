<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\StorytellingContentMapper;
use App\DTOs\Cms\StorytellingEventCmsData;
use App\DTOs\Cms\StorytellingMasonrySectionContent;

class StorytellingContentRepository extends BaseContentRepository implements Interfaces\IStorytellingContentRepository
{
    public function findMasonryContent(string $pageSlug, string $sectionKey): StorytellingMasonrySectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return StorytellingContentMapper::mapMasonry($raw);
    }

    public function findEventCmsData(string $pageSlug, string $sectionKey): StorytellingEventCmsData
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return StorytellingContentMapper::mapEventCmsData($raw);
    }
}
