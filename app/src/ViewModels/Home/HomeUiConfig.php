<?php

declare(strict_types=1);

namespace App\ViewModels\Home;

final class HomeUiConfig
{
    public const BADGE_COLORS = [
        'jazz' => 'bg-azure-blue-80',
        'dance' => 'bg-deep-crimson-80',
        'history' => 'bg-amber-gold-80',
        'restaurant' => 'bg-olive-green-80',
        'storytelling' => 'bg-deep-purple-80',
    ];

    public const SCHEDULE_COLORS = [
        'jazz' => 'bg-azure-blue',
        'dance' => 'bg-deep-crimson',
        'history' => 'bg-amber-gold',
        'restaurant' => 'bg-olive-green',
        'storytelling' => 'bg-deep-purple',
    ];

    public const EVENT_TYPE_ORDER = ['jazz', 'dance', 'history', 'restaurant', 'storytelling'];

    public const SECTION_MAP = [
        'jazz' => 'event_jazz',
        'dance' => 'event_dance',
        'history' => 'event_history',
        'restaurant' => 'event_restaurant',
        'storytelling' => 'event_storytelling',
    ];

    public const DARK_BG_MAP = [
        'jazz' => true,
        'dance' => false,
        'history' => true,
        'restaurant' => false,
        'storytelling' => true,
    ];

    public const EVENT_SUMMARY_TITLES = [
        'jazz' => 'Haarlem Jazz @ Patronaat',
        'dance' => 'DANCE! (Back2Back & Club Sessions)',
        'history' => 'A Stroll through History (Tour)',
        'restaurant' => 'Yummy! Dinner Sessions',
        'storytelling' => 'Stories in Haarlem',
    ];

    public const PLACEHOLDER_DATES = ['2026-07-25', '2026-07-26', '2026-07-27', '2026-07-28'];

    private function __construct()
    {
    }
}
