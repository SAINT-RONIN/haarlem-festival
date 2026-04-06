<?php

declare(strict_types=1);

namespace App\Constants;

/**
 * Shared CMS section key patterns used across multiple detail page features.
 * Centralises the event section key format so Jazz and Storytelling detail pages
 * derive their section keys from a single source.
 */
final class SharedSectionKeys
{
    public const SECTION_HERO = 'hero_section';
    public const SECTION_GRADIENT = 'gradient_section';
    public const SECTION_INTRO = 'intro_section';
    public const SECTION_INTRO_SPLIT = 'intro_split_section';

    public const EVENT_SECTION_PREFIX = 'event_';

    /** Builds the CMS section key for a given event ID (e.g. "event_42"). */
    public static function eventSectionKey(int $eventId): string
    {
        return self::EVENT_SECTION_PREFIX . $eventId;
    }

    private function __construct()
    {
    }
}
