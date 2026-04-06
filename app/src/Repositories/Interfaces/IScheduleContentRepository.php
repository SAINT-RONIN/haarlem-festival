<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\DTOs\Cms\ScheduleSectionContent;

/**
 * Typed access to Schedule page CMS content sections.
 */
interface IScheduleContentRepository
{
    /** Fetches the schedule section content as a typed object. */
    public function findScheduleSectionContent(string $pageSlug, string $sectionKey): ScheduleSectionContent;
}
