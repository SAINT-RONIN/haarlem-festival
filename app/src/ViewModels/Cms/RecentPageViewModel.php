<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * ViewModel for a recently updated page in the CMS dashboard.
 */
final readonly class RecentPageViewModel
{
    public function __construct(
        public string $title,
        public string $status,
        public string $timeAgo,
    ) {
    }
}
