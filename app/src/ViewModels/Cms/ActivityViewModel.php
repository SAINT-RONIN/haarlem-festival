<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * ViewModel for a recent activity item in the CMS dashboard.
 */
final readonly class ActivityViewModel
{
    public function __construct(
        public string $icon,
        public string $text,
        public string $time,
        public string $color,
    )
    {
    }
}

