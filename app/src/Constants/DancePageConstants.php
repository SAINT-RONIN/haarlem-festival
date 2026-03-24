<?php

declare(strict_types=1);

namespace App\Constants;

final class DancePageConstants
{
    public const PAGE_SLUG = 'dance';
    public const CURRENT_PAGE = 'dance';
    public const SCHEDULE_MAX_DAYS = 7;

    public const SECTION_GRADIENT = 'gradient_section';
    public const SECTION_INTRO = 'intro_split_section';
    public const SECTION_EXPERIENCE = 'experience_section';

    public const DEFAULT_HERO_BACKGROUND_IMAGE = '/assets/Image/Image (Dance).png';
    public const DEFAULT_GRADIENT_BACKGROUND_IMAGE = '/assets/Image/dance/banner.jpg';
    public const DEFAULT_INTRO_IMAGE = '/assets/Image/dance-crowd-stage.jpg';
    public const DEFAULT_INTRO_IMAGE_ALT = 'Dance festival crowd in front of the stage';

    private function __construct()
    {
    }
}