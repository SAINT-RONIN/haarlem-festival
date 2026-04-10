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

    public const DEFAULT_IMAGE = '/assets/Image/Image (Yummy).png';
    public const RESERVATION_FEE = 10.00;
    public const VALID_DATES = ['Thursday', 'Friday', 'Saturday', 'Sunday'];
    public const MAX_SPECIAL_REQUESTS_LENGTH = 1000;
    public const MAX_GUEST_COUNT = 20;

    private function __construct() {}
}
