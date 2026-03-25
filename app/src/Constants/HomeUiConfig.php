<?php

declare(strict_types=1);

namespace App\Constants;

/**
 * UI configuration for the home page event types.
 * Consolidates badge colour, schedule colour, section key, dark-background flag,
 * and summary title into a single map keyed by event type slug.
 */
final class HomeUiConfig
{
    /**
     * Unified configuration per event type. Each entry holds all display attributes
     * so callers can look up one slug once instead of querying five parallel arrays.
     *
     * @var array<string, array{badgeColor: string, scheduleColor: string, sectionKey: string, darkBg: bool, summaryTitle: string}>
     */
    public const EVENT_TYPE_CONFIG = [
        'jazz' => [
            'badgeColor'    => 'bg-azure-blue-80',
            'scheduleColor' => 'bg-azure-blue',
            'sectionKey'    => 'event_jazz',
            'darkBg'        => true,
            'summaryTitle'  => 'Haarlem Jazz @ Patronaat',
        ],
        'dance' => [
            'badgeColor'    => 'bg-deep-crimson-80',
            'scheduleColor' => 'bg-deep-crimson',
            'sectionKey'    => 'event_dance',
            'darkBg'        => false,
            'summaryTitle'  => 'DANCE! (Back2Back & Club Sessions)',
        ],
        'history' => [
            'badgeColor'    => 'bg-amber-gold-80',
            'scheduleColor' => 'bg-amber-gold',
            'sectionKey'    => 'event_history',
            'darkBg'        => true,
            'summaryTitle'  => 'A Stroll through History (Tour)',
        ],
        'restaurant' => [
            'badgeColor'    => 'bg-olive-green-80',
            'scheduleColor' => 'bg-olive-green',
            'sectionKey'    => 'event_restaurant',
            'darkBg'        => false,
            'summaryTitle'  => 'Yummy! Dinner Sessions',
        ],
        'storytelling' => [
            'badgeColor'    => 'bg-deep-purple-80',
            'scheduleColor' => 'bg-deep-purple',
            'sectionKey'    => 'event_storytelling',
            'darkBg'        => true,
            'summaryTitle'  => 'Stories in Haarlem',
        ],
    ];

    /** Ordered list of event type slugs for home page rendering. */
    public const EVENT_TYPE_ORDER = ['jazz', 'dance', 'history', 'restaurant', 'storytelling'];

    /** Fallback dates when no live sessions exist yet. */
    public const PLACEHOLDER_DATES = ['2026-07-25', '2026-07-26', '2026-07-27', '2026-07-28'];

    private function __construct()
    {
    }
}
