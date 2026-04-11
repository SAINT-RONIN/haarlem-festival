<?php

declare(strict_types=1);

namespace App\Constants;

final class DancePageConstants
{
    public const PAGE_SLUG = 'dance';

    public const SECTION_HEADLINERS = 'headliners_section';
    public const SECTION_ARTISTS    = 'artists_section';

    public const DEFAULT_HERO_BACKGROUND_IMAGE = '/assets/Image/Image (Dance).png';
    public const DEFAULT_GRADIENT_BACKGROUND_IMAGE = '/assets/Image/Image (Dance).png';
    public const DEFAULT_INTRO_IMAGE     = '/assets/Image/Image (Dance).png';
    public const DEFAULT_INTRO_IMAGE_ALT = 'Dancers performing at Haarlem Dance Festival';

    /** Artists with CardSortOrder ≤ this value are shown as headliners. */
    public const HEADLINER_MAX_COUNT = 2;

    private function __construct() {}
}
