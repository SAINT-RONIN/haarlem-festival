<?php

declare(strict_types=1);

namespace App\Constants;

/**
 * Groups all magic values used across the Storytelling detail page feature.
 * The reason for this is because the detail page uses different CMS slugs and section key patterns from the overview page, so keeping them separate avoids confusion and makes each page's constants easy to find.
 */
final class StorytellingDetailConstants
{
    public const DETAIL_PAGE_SLUG = 'storytelling-detail';
    public const SCHEDULE_PAGE_SLUG = 'storytelling';

    private function __construct()
    {
    }
}
