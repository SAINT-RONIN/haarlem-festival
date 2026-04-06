<?php

declare(strict_types=1);

namespace App\Constants;

/**
 * CSS class mappings for the CMS page editor accordion sections.
 * Centralises Tailwind colour and grid classes so views only read constants.
 */
final class CmsEditorStyles
{
    /** @var array<string, array{border: string, bg: string, text: string, icon: string}> */
    public const COLOR_MAP = [
        'blue'    => ['border' => 'border-l-blue-500', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'icon' => 'text-blue-500'],
        'amber'   => ['border' => 'border-l-amber-500', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'icon' => 'text-amber-500'],
        'emerald' => ['border' => 'border-l-emerald-500', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'icon' => 'text-emerald-500'],
        'purple'  => ['border' => 'border-l-purple-500', 'bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'icon' => 'text-purple-500'],
        'rose'    => ['border' => 'border-l-rose-500', 'bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'icon' => 'text-rose-500'],
    ];

    public const DEFAULT_COLOR = self::COLOR_MAP['blue'];

    /** @var array<int, string> */
    public const COLUMN_CLASS_MAP = [
        1 => 'grid-cols-1',
        2 => 'grid-cols-1 md:grid-cols-2',
        3 => 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    ];
}
