<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * A single activity entry in the CMS dashboard feed — icon, text, timestamp, and color.
 */
final readonly class ActivityViewModel
{
    public function __construct(
        public string $icon,
        public string $text,
        public string $time,
        public string $color,
    ) {
    }
}
