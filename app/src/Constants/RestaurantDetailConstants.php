<?php

declare(strict_types=1);

namespace App\Constants;

/**
 * Constants for the Restaurant detail page feature.
 *
 * Per-restaurant CMS content is stored as individual sections keyed by event ID
 * (event_{id}) under the 'restaurant-detail' page — the same pattern as Storytelling.
 */
final class RestaurantDetailConstants
{
    public const PAGE_SLUG            = 'restaurant-detail';
    public const EVENT_SECTION_PREFIX = SharedSectionKeys::EVENT_SECTION_PREFIX;

    public static function eventSectionKey(int $eventId): string
    {
        return SharedSectionKeys::eventSectionKey($eventId);
    }

    private function __construct() {}
}