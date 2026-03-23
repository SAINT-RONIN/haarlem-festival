<?php

declare(strict_types=1);

namespace App\Constants;

final class JazzPageConstants
{
    public const PAGE_SLUG = 'jazz';
    public const CURRENT_PAGE = 'jazz';
    public const SCHEDULE_MAX_DAYS = 7;

    public const SECTION_HERO = 'hero_section';
    public const SECTION_GRADIENT = 'gradient_section';
    public const SECTION_INTRO = 'intro_section';
    public const SECTION_VENUES = 'venues_section';
    public const SECTION_PRICING = 'pricing_section';
    public const SECTION_SCHEDULE_CTA = 'schedule_cta_section';
    public const SECTION_ARTISTS = 'artists_section';
    public const SECTION_BOOKING_CTA = 'booking_cta_section';

    public const DEFAULT_HERO_BACKGROUND_IMAGE = '/assets/Image/Jazz/Jazz-hero.png';
    public const DEFAULT_GRADIENT_BACKGROUND_IMAGE = '/assets/Image/Jazz/Jazz-second-section.png';
    public const DEFAULT_INTRO_IMAGE = '/assets/Image/Jazz/Jazz-third-section.png';
    public const DEFAULT_INTRO_IMAGE_ALT = 'Jazz musicians performing at Haarlem Festival';

    public const DEFAULT_GUMBO_KINGS_IMAGE = '/assets/Image/Jazz/Jazz-Gumbokings.png';
    public const DEFAULT_EVOLVE_IMAGE = '/assets/Image/Jazz/Jazz-evolve.png';
    public const DEFAULT_NTJAM_IMAGE = '/assets/Image/Jazz/Jazz-Ntjam.png';

    public const ARTISTS_CURRENT_PAGE = 1;
    public const ARTISTS_TOTAL_PAGES = 4;
    public const ARTISTS_TOTAL_COUNT = 12;

    private function __construct()
    {
    }
}
