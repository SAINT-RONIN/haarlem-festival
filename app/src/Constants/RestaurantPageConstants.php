<?php

declare(strict_types=1);

namespace App\Constants;

final class RestaurantPageConstants
{
    public const PAGE_SLUG = 'restaurant';
    public const DETAIL_PAGE_SLUG = 'restaurant-detail';

    public const SECTION_INTRO_SPLIT2 = 'intro_split2_section';
    public const SECTION_INSTRUCTIONS = 'instructions_section';
    public const SECTION_CARDS = 'restaurant_cards_section';
    public const SECTION_DETAIL = 'detail_section';

    public const DEFAULT_IMAGE = '/assets/Image/Image (Yummy).png';
    public const MAX_SPECIAL_REQUESTS_LENGTH = 1000;
    public const MAX_GUEST_COUNT = 20;

    // Fallback defaults when CMS values are not set
    public const DEFAULT_RESERVATION_FEE = 10.00;
    public const DEFAULT_VALID_DATES = ['Thursday', 'Friday', 'Saturday', 'Sunday'];

    private function __construct() {}
}
