<?php

declare(strict_types=1);

namespace App\Constants;

final class JazzArtistDetailConstants
{
    public const DETAIL_PAGE_SLUG = 'jazz-artist-detail';
    public const SCHEDULE_PAGE_SLUG = 'jazz';

    public const EVENT_SECTION_PREFIX = 'event_';
    public const LINEUP_PREFIX = 'lineup_';
    public const HIGHLIGHT_PREFIX = 'highlight_';
    public const GALLERY_IMAGE_PREFIX = 'gallery_image_';
    public const ALBUM_PREFIX = 'album_';
    public const TRACK_PREFIX = 'track_';

    public const SCHEDULE_MAX_DAYS = 7;
    public const MAX_ALBUMS = 8;
    public const MAX_TRACKS = 12;
    public const MAX_LIST_ITEMS = 24;
    public const PAGE_CACHE_TTL_SECONDS = 300;

    public static function eventSectionKey(int $eventId): string
    {
        return self::EVENT_SECTION_PREFIX . $eventId;
    }

    private function __construct()
    {
    }
}

