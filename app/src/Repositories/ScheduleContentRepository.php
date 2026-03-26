<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\ScheduleContentMapper;
use App\Models\ScheduleSectionContent;
use App\Repositories\Interfaces\ICmsContentRepository;

/**
 * Provides typed access to Schedule page CMS content sections.
 *
 * Wraps the generic ICmsContentRepository and delegates field mapping
 * to ScheduleContentMapper.
 */
class ScheduleContentRepository
{
    public function __construct(
        private readonly ICmsContentRepository $cmsContent,
    ) {
    }

    /** Fetches the schedule section content as a typed object. */
    public function findScheduleSectionContent(string $pageSlug, string $sectionKey): ScheduleSectionContent
    {
        $raw = $this->cmsContent->getSectionContent($pageSlug, $sectionKey);
        return ScheduleContentMapper::mapScheduleSection($raw);
    }
}
