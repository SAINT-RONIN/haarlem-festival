<?php

declare(strict_types=1);

namespace App\ViewModels\Cms;

/**
 * ViewModel for the CMS Dashboard page.
 *
 * Contains all data needed to render the dashboard view.
 */
final readonly class DashboardViewModel
{
    /**
     * @param RecentPageViewModel[] $recentPages
     * @param ActivityViewModel[] $activities
     */
    public function __construct(
        public array  $recentPages,
        public array  $activities,
        public string $userName,
    )
    {
    }
}

