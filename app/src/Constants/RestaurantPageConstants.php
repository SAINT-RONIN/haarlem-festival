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
    // Shared "Make your reservation" image used by every restaurant detail page.
    public const RESERVATION_IMAGE = '/assets/Image/restaurants/ratatouille-reservation.jpg';
    public const MAX_SPECIAL_REQUESTS_LENGTH = 1000;
    public const MAX_GUEST_COUNT = 20;

    private function __construct() {}
}
