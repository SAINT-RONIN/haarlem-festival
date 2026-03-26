<?php

declare(strict_types=1);

namespace App\Constants;

/**
 * Groups all magic values used across the Restaurant page feature.
 * Centralising page slugs and section keys here prevents scattering
 * string literals through services and mappers.
 */
final class RestaurantPageConstants
{
    public const PAGE_SLUG = 'restaurant';

    public const SECTION_INTRO_SPLIT2 = 'intro_split2_section';
    public const SECTION_INSTRUCTIONS = 'instructions_section';
    public const SECTION_CARDS = 'restaurant_cards_section';
    public const SECTION_DETAIL = 'detail_section';

    private function __construct()
    {
    }
}
