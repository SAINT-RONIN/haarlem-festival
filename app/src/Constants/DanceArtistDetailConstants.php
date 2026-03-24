<?php

declare(strict_types=1);

namespace App\Constants;

final class DanceArtistDetailConstants
{
    public const DETAIL_PAGE_SLUG = 'dance-artist-detail';
    public const SCHEDULE_PAGE_SLUG = 'dance';
    public const EVENT_SECTION_PREFIX = 'event_';
    public const SCHEDULE_MAX_DAYS = 7;

    public static function eventSectionKey(int $eventId): string
    {
        return self::EVENT_SECTION_PREFIX . $eventId;
    }

    private function __construct()
    {
    }
}