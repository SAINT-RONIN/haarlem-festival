<?php

declare(strict_types=1);

namespace App\Constants;

/**
 * Groups all magic values used across the Storytelling overview page feature.
 * The reason for this is because scattering string literals like page slugs and section keys through services and mappers makes refactoring fragile, so one central constants class is the single source of truth.
 */
final class StorytellingPageConstants
{
    public const PAGE_SLUG = 'storytelling';
    public const CURRENT_PAGE = 'storytelling';
    public const SCHEDULE_MAX_DAYS = 7;

    public const SECTION_HERO = 'hero_section';
    public const SECTION_GRADIENT = 'gradient_section';
    public const SECTION_INTRO_SPLIT = 'intro_split_section';
    public const SECTION_MASONRY = 'masonry_section';

    private function __construct()
    {
    }
}
