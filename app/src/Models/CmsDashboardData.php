<?php

declare(strict_types=1);

namespace App\Models;

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
