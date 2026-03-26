<?php

declare(strict_types=1);

namespace App\Constants;

/**
 * Constants for the Restaurant detail page feature.
 *
 * Mirrors StorytellingDetailConstants: per-restaurant content is stored as
 * individual CMS sections keyed by event ID (restaurant_event_{id}), all
 * under the shared 'restaurant' page slug.
 */
final class RestaurantDetailConstants
{
    public const PAGE_SLUG            = 'restaurant';
    public const EVENT_SECTION_PREFIX = 'restaurant_event_';

    public static function eventSectionKey(int $eventId): string
    {
        return self::EVENT_SECTION_PREFIX . $eventId;
    }

    private function __construct() {}
}