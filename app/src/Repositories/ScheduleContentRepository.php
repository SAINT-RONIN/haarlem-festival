<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\ScheduleContentMapper;
use App\Content\ScheduleSectionContent;

/**
 * Provides typed access to Schedule page CMS content sections.
 *
 * Wraps the generic ICmsContentRepository and delegates field mapping
 * to ScheduleContentMapper.
 */
class ScheduleContentRepository extends BaseContentRepository implements Interfaces\IScheduleContentRepository
{
    /** Fetches the schedule section content as a typed object. */
    public function findScheduleSectionContent(string $pageSlug, string $sectionKey): ScheduleSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return ScheduleContentMapper::mapScheduleSection($raw);
    }
}
