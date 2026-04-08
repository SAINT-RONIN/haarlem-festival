<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * A single recently-edited page card on the CMS dashboard.
 */
final readonly class RecentPageViewModel
{
    public function __construct(
        public string $title,
        public string $status,
        public string $timeAgo,
    ) {}
}
