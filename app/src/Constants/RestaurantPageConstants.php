<?php

declare(strict_types=1);

namespace App\Constants;

/**
 * Groups all magic values used across the Yummy restaurant page feature.
 * Scattering string literals like page slugs and section keys through services
 * and mappers makes refactoring fragile, so one central constants class is the
 * single source of truth.
 */
final class RestaurantPageConstants
{
    public const PAGE_SLUG    = 'restaurant';
    public const CURRENT_PAGE = 'restaurant';

    // CMS section keys
    public const SECTION_GRADIENT     = 'gradient_section';
    public const SECTION_INTRO_SPLIT  = 'intro_split_section';
    public const SECTION_INTRO_SPLIT2 = 'intro_split2_section';
    public const SECTION_INSTRUCTIONS = 'instructions_section';
    public const SECTION_CARDS        = 'restaurant_cards_section';
    public const SECTION_DETAIL       = 'detail_section';

    // Fallback shown when no media asset is linked
    public const DEFAULT_IMAGE = '/assets/Image/Image (Yummy).png';

    // Reservation fee charged per guest at booking time (deducted from final bill)
    public const RESERVATION_FEE = 10.00;

    // Valid festival days shown in the reservation date selector
    public const VALID_DATES = ['Thursday', 'Friday', 'Saturday', 'Sunday'];

    private function __construct()
    {
    }
}