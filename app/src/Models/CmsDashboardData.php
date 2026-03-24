<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Bundles recent pages and activity data for the CMS dashboard landing page.
 */
final readonly class CmsDashboardData
{
    /**
     * @param CmsPage[]      $recentPages
     * @param ActivityData[] $activities
     */
    public function __construct(
        public array $recentPages,
        public array $activities,
    ) {}
}
