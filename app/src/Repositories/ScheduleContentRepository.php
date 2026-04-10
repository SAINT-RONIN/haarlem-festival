<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Mappers\ScheduleContentMapper;
use App\DTOs\Cms\ScheduleSectionContent;

class ScheduleContentRepository extends BaseContentRepository implements Interfaces\IScheduleContentRepository
{
    public function findScheduleSectionContent(string $pageSlug, string $sectionKey): ScheduleSectionContent
    {
        $raw = $this->fetchSectionContent($pageSlug, $sectionKey);
        return ScheduleContentMapper::mapScheduleSection($raw);
    }
}
